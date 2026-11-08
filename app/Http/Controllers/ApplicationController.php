<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\Offer;
use App\Models\Practice;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    use HasDataTable;

    /**
     * Postulaciones para Administrador (todas) y Coordinador (solo las de su carrera).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Application::query()
            ->with([
                'student.user:id,name,last_name,email',
                'student.career:id,codigo,nombre',
                'offer:id,titulo,empresa,empresa_id,vacantes',
                'offer.company:id,razon_social',
                'practice:id,application_id',
            ])
            ->orderByDesc('matching_score');

        if ($user?->hasRole('Coordinador')) {
            $query->whereHas('student', fn (Builder $student) => $student->where('career_id', $user->career_id));
        }

        $applications = $query->get();

        $counts = [
            'total' => $applications->count(),
            'postuladas' => $applications->where('status', 'Postulado')->count(),
            'asignadas' => $applications->whereNotIn('status', ['Postulado', 'Rechazado'])->count(),
            'rechazadas' => $applications->where('status', 'Rechazado')->count(),
        ];

        $canDelete = (bool) $user?->hasAnyRole(['Administrador', 'Super Administrador']);

        return view('applications.index', compact('applications', 'counts', 'canDelete'));
    }

    /**
     * Datos en JSON para la vista "Mis postulaciones" del estudiante.
     */
    public function studentData(Request $request): JsonResponse
    {
        $query = Application::query()
            ->with([
                'student.user:id,name,last_name,email',
                'student.career:id,nombre',
                'offer.company:id,razon_social',
            ])
            ->whereHas('student', fn (Builder $student) => $student->where('user_id', $request->user()->id));

        return $this->dataTableResponse(
            $request,
            $query,
            searchable: ['status', 'matching_score'],
            sortable: [
                'id' => 'id',
                'estado' => 'status',
                'matching_score' => 'matching_score',
                'created_at' => 'created_at',
            ],
            transform: fn (Application $application) => $this->format($application),
            defaultSort: 'created_at',
            defaultDir: 'desc',
            extraSearch: fn ($query, $like) => $query
                ->orWhereHas('offer', fn ($offer) => $offer->where('titulo', 'like', $like)),
        );
    }

    public function store(ApplicationRequest $request): JsonResponse
    {
        $student = $request->user()?->studentProfile;
        if (! $student instanceof Student || ! $student->career_id) {
            throw ValidationException::withMessages(['student' => 'Completa tu perfil de estudiante y selecciona una carrera antes de postular.']);
        }

        $validated = $request->validated();
        $cvPath = $request->file('cv')->store('cvs', 'local');

        try {
            $application = DB::transaction(function () use ($student, $validated, $cvPath): Application {
                $offer = Offer::query()->lockForUpdate()->findOrFail($validated['offer_id']);
                if ($offer->estado !== 'Aprobada') {
                    throw ValidationException::withMessages(['offer_id' => 'La oferta no está aprobada para postular.']);
                }
                if ($offer->applications()->whereHas('practice')->count() >= $offer->vacantes) {
                    throw ValidationException::withMessages(['offer_id' => 'La oferta ya no tiene cupos disponibles.']);
                }
                if ($offer->careers()->exists() && ! $offer->careers()->whereKey($student->career_id)->exists()) {
                    throw ValidationException::withMessages(['offer_id' => 'La oferta no corresponde a tu carrera.']);
                }

                $application = Application::create([
                    'student_id' => $student->id,
                    'offer_id' => $offer->id,
                    'status' => 'Postulado',
                    'cv_path' => $cvPath,
                    'motivation' => $validated['carta_motivacion'],
                    'matching_score' => $this->matchingScore($student, $offer),
                ]);

                return $application->load(['student.user', 'student.career', 'offer.company']);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($cvPath);
            throw $exception;
        }

        return response()->json($this->format($application), 201);
    }

    /**
     * Asigna la postulación: la marca como "Asignado" y crea la práctica.
     */
    public function assign(Request $request, Application $application): RedirectResponse
    {
        $error = DB::transaction(function () use ($request, $application): ?string {
            $application = Application::query()->lockForUpdate()->findOrFail($application->id);
            $offer = Offer::query()->lockForUpdate()->findOrFail($application->offer_id);
            $this->authorizeReview($request, $application);

            if ($application->status !== 'Postulado') {
                return 'Solo se pueden asignar postulaciones pendientes.';
            }
            if ($offer->applications()->whereHas('practice')->count() >= $offer->vacantes) {
                return "La oferta «{$offer->titulo}» ya completó sus cupos.";
            }

            $application->update([
                'status' => 'Asignado',
                'reviewed_by' => $request->user()->id,
                'decision_at' => now(),
            ]);

            Practice::create([
                'application_id' => $application->id,
                'assigned_by' => $request->user()->id,
                'estado' => 'Asignado',
            ]);

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Postulación asignada. La práctica quedó creada en estado «Asignado».');
    }

    public function reject(Request $request, Application $application): RedirectResponse
    {
        $this->authorizeReview($request, $application);

        if ($application->status !== 'Postulado') {
            return back()->with('error', 'La postulación ya fue resuelta.');
        }

        $application->update([
            'status' => 'Rechazado',
            'reviewed_by' => $request->user()->id,
            'decision_at' => now(),
        ]);

        return back()->with('success', 'Postulación rechazada.');
    }

    /**
     * Eliminación desde administración (solo Administrador).
     */
    public function destroy(Request $request, Application $application): RedirectResponse
    {
        abort_unless($request->user()->hasAnyRole(['Administrador', 'Super Administrador']), 403);

        if ($application->practice()->exists()) {
            return back()->with('error', 'No se puede eliminar una postulación que ya tiene práctica asignada. Elimina primero la práctica.');
        }

        $this->deleteApplication($application);

        return back()->with('success', 'Postulación eliminada correctamente.');
    }

    /**
     * El estudiante retira su propia postulación mientras siga pendiente.
     */
    public function studentDestroy(Request $request, Application $application): Response
    {
        abort_unless($application->student()->where('user_id', $request->user()->id)->exists(), 404);
        abort_unless($application->status === 'Postulado', 409, 'No puedes retirar una postulación ya asignada.');

        $this->deleteApplication($application);

        return response()->noContent();
    }

    private function deleteApplication(Application $application): void
    {
        if ($application->cv_path) {
            Storage::disk('local')->delete($application->cv_path);
        }
        $application->delete();
    }

    public function cv(Request $request, Application $application): StreamedResponse
    {
        abort_unless(filled($application->cv_path), 404);
        $user = $request->user();
        $ownsApplication = $application->student()->where('user_id', $user->id)->exists();
        $ownsCompany = $application->offer()->whereHas('company', fn (Builder $company) => $company->where('owner_user_id', $user->id))->exists();
        $coordinatesCareer = $user->hasRole('Coordinador')
            && $application->student()->where('career_id', $user->career_id)->exists();

        abort_unless(
            $ownsApplication || $ownsCompany || $coordinatesCareer || $user->hasAnyRole(['Administrador', 'Super Administrador']),
            403
        );

        return Storage::disk('local')->download($application->cv_path);
    }

    private function matchingScore(Student $student, Offer $offer): float
    {
        $offerSkills = collect($offer->habilidades ?? [])->map(fn (string $skill) => mb_strtolower(trim($skill)))->unique();
        $studentSkills = collect($student->habilidades ?? [])->map(fn (string $skill) => mb_strtolower(trim($skill)))->unique();
        $careerScore = $offer->careers()->whereKey($student->career_id)->exists() ? 40 : 0;
        $levelScore = mb_strtolower($student->nivel_avance) === mb_strtolower($offer->nivel_requerido ?? '') ? 25 : 0;
        $skillScore = $offerSkills->isEmpty() ? 35 : 35 * $offerSkills->intersect($studentSkills)->count() / $offerSkills->count();

        return round($careerScore + $levelScore + $skillScore, 2);
    }

    private function authorizeReview(Request $request, Application $application): void
    {
        $user = $request->user();
        if ($user->hasAnyRole(['Administrador', 'Super Administrador'])) {
            return;
        }

        abort_unless(
            $user->hasRole('Coordinador')
                && $application->student()->where('career_id', $user->career_id)->exists(),
            403,
        );
    }

    private function format(Application $application): array
    {
        return [
            'id' => $application->id,
            'student' => trim(($application->student?->user?->name ?? '').' '.($application->student?->user?->last_name ?? '')),
            'career' => $application->student?->career?->nombre ?? $application->student?->carrera,
            'offer' => $application->offer?->titulo,
            'company' => $application->offer?->company?->razon_social ?? $application->offer?->empresa,
            'estado' => $application->status,
            'matching_score' => (float) $application->matching_score,
            'cv_url' => $application->cv_path ? route('applications.cv', $application) : null,
            'created_at' => $application->created_at?->toDateString(),
        ];
    }
}
