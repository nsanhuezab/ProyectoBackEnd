<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\Offer;
use App\Models\Practice;
use App\Models\Student;
use App\Support\Notifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Postulaciones: listado, asignación, rechazo, mensajes y descarga del CV.
 */
class ApplicationController extends Controller
{
    /**
     * Lista de postulaciones. El coordinador solo ve las de su carrera.
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
                if ($offer->min_gpa !== null && ($student->gpa === null || (float) $student->gpa < (float) $offer->min_gpa)) {
                    throw ValidationException::withMessages(['offer_id' => 'Esta oferta exige un promedio mínimo de '.number_format((float) $offer->min_gpa, 1, ',', '').' y tu promedio registrado no lo alcanza.']);
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

        Notifier::send(
            Notifier::coordinatorsOf([$student->career_id]),
            'Nueva postulación',
            trim($request->user()->name.' '.$request->user()->last_name)." postuló a «{$application->offer?->titulo}» con ".round((float) $application->matching_score).'% de afinidad.',
            route('applications.show', $application),
        );

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

        $application->loadMissing('student.user', 'offer');
        Notifier::send(
            $application->student?->user,
            '¡Te asignaron una práctica!',
            "Tu postulación a «{$application->offer?->titulo}» fue aceptada. Revisa los detalles en «Mi práctica».",
            route('student.practice.index'),
        );

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

        $application->loadMissing('student.user', 'offer');
        Notifier::send(
            $application->student?->user,
            'Postulación no seleccionada',
            "Tu postulación a «{$application->offer?->titulo}» no fue seleccionada esta vez.",
            route('applications.show', $application),
        );

        return back()->with('success', 'Postulación rechazada.');
    }

    /**
     * Elimina una postulación (solo administrador).
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
    public function studentDestroy(Request $request, Application $application): RedirectResponse
    {
        abort_unless($application->student()->where('user_id', $request->user()->id)->exists(), 404);

        if ($application->status !== 'Postulado') {
            return back()->with('error', 'No puedes retirar una postulación que ya fue resuelta.');
        }

        $this->deleteApplication($application);

        return back()->with('success', 'Postulación retirada.');
    }

    private function deleteApplication(Application $application): void
    {
        if ($application->cv_path) {
            Storage::disk('local')->delete($application->cv_path);
        }
        $application->delete();
    }

    /**
     * Detalle de la postulación con sus mensajes.
     */
    public function show(Request $request, Application $application): View
    {
        $application->load([
            'student.user:id,name,last_name,email',
            'student.career',
            'offer.company',
            'practice',
            'reviewer:id,name,last_name',
            'messages.user:id,name,last_name',
        ]);
        $this->authorizeConversation($request, $application);

        $isStudent = $application->student?->user_id === $request->user()->id;
        $canReview = ! $isStudent && $application->status === 'Postulado';

        return view('applications.show', compact('application', 'isStudent', 'canReview'));
    }

    public function storeMessage(Request $request, Application $application): RedirectResponse
    {
        $application->loadMissing('student.user', 'offer');
        $this->authorizeConversation($request, $application);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ], [], ['body' => 'mensaje']);

        ApplicationMessage::create([
            'application_id' => $application->id,
            'user_id' => $request->user()->id > 0 ? $request->user()->id : null,
            'application_status' => $application->status,
            'body' => $validated['body'],
        ]);

        $fromStudent = $application->student?->user_id === $request->user()->id;
        Notifier::send(
            $fromStudent ? Notifier::coordinatorsOf([$application->student?->career_id]) : $application->student?->user,
            'Nuevo mensaje en una postulación',
            trim($request->user()->name.' '.$request->user()->last_name)." escribió sobre «{$application->offer?->titulo}».",
            route('applications.show', $application),
        );

        return back()->with('success', 'Mensaje enviado.');
    }

    private function authorizeConversation(Request $request, Application $application): void
    {
        $user = $request->user();
        abort_unless(
            $application->student?->user_id === $user->id
                || $user->hasAnyRole(['Administrador', 'Super Administrador'])
                || ($user->hasRole('Coordinador') && $application->student?->career_id === $user->career_id),
            403,
        );
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
            'detail_url' => route('applications.show', $application),
            'created_at' => $application->created_at?->toDateString(),
        ];
    }
}
