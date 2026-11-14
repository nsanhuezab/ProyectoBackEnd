<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Application;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * API privada 1: postulaciones. Cada rol solo ve lo suyo.
 */
class ApplicationApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'status' => ['nullable', 'string', 'max:40'],
        ]);

        $query = $this->scoped($request->user())
            ->with(['student.user:id,name,last_name', 'student.career:id,nombre', 'offer:id,titulo,empresa,empresa_id', 'offer.company:id,razon_social']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $page = $query->orderByDesc('id')->paginate($this->perPage($request));

        return $this->paginated($page, fn (Application $a) => $this->format($a));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $application = $this->scoped($request->user())
            ->with(['student.user:id,name,last_name', 'student.career:id,nombre', 'offer.company:id,razon_social', 'messages'])
            ->find($id);

        if (! $application) {
            return $this->notFound('Postulación');
        }

        $data = $this->format($application);
        $data['motivacion'] = $application->motivation;
        $data['mensajes'] = $application->messages->map(fn ($m) => [
            'id' => $m->id,
            'user_id' => $m->user_id,
            'estado_postulacion' => $m->application_status,
            'mensaje' => $m->body,
            'fecha' => $m->created_at?->toIso8601String(),
        ])->all();

        return response()->json(['data' => $data]);
    }

    /**
     * Funcionalidad crítica: el estudiante postula a una oferta aprobada (multipart/form-data).
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasRole('Estudiante')) {
            return response()->json(['message' => 'Solo los estudiantes pueden postular.'], 403);
        }

        $student = $user->studentProfile;
        if (! $student instanceof Student || ! $student->career_id) {
            throw ValidationException::withMessages(['student' => 'Completa tu perfil de estudiante y selecciona una carrera antes de postular.']);
        }

        $validated = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:offers,id'],
            'carta_motivacion' => ['required', 'string', 'max:10000'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        if (Application::where('student_id', $student->id)->where('offer_id', $validated['offer_id'])->exists()) {
            throw ValidationException::withMessages(['offer_id' => 'Ya postulaste a esta oferta.']);
        }

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

                return $application->load(['student.user:id,name,last_name', 'student.career:id,nombre', 'offer.company:id,razon_social']);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($cvPath);
            throw $exception;
        }

        Notifier::send(
            Notifier::coordinatorsOf([$student->career_id]),
            'Nueva postulación',
            trim($user->name.' '.$user->last_name).' postuló a «'.$application->offer?->titulo.'» con '.round((float) $application->matching_score).'% de afinidad.',
            route('applications.show', $application),
        );

        return response()->json(['data' => $this->format($application)], 201);
    }

    /**
     * El estudiante retira su postulación mientras siga en estado «Postulado».
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasRole('Estudiante')) {
            return response()->json(['message' => 'Solo los estudiantes pueden retirar postulaciones.'], 403);
        }

        $application = $this->scoped($user)->find($id);
        if (! $application) {
            return $this->notFound('Postulación');
        }
        if ($application->status !== 'Postulado') {
            return response()->json(['message' => 'No puedes retirar una postulación que ya fue resuelta.'], 409);
        }

        if ($application->cv_path) {
            Storage::disk('local')->delete($application->cv_path);
        }
        $application->delete();

        return response()->json(['message' => 'Postulación retirada.']);
    }

    // ------------------------------------------------------------------ helpers

    /**
     * Postulaciones visibles según el rol del usuario.
     */
    private function scoped(User $user): Builder
    {
        $query = Application::query();

        return match (true) {
            $this->isAdmin($user) => $query,
            $user->hasRole('Coordinador') => $query->whereHas('student', fn ($s) => $s->where('career_id', $user->career_id)),
            $user->hasRole('Empresa') => $query->whereHas('offer.company', fn ($c) => $c->where('owner_user_id', $user->id)),
            $user->hasRole('Estudiante') => $query->whereHas('student', fn ($s) => $s->where('user_id', $user->id)),
            default => abort(403, 'Tu rol no tiene acceso a las postulaciones.'),
        };
    }

    private function format(Application $application): array
    {
        return [
            'id' => $application->id,
            'estado' => $application->status,
            'afinidad' => $application->matching_score !== null ? (float) $application->matching_score : null,
            'tiene_cv' => filled($application->cv_path),
            'fecha_postulacion' => $application->created_at?->toIso8601String(),
            'fecha_decision' => $application->decision_at?->toIso8601String(),
            'estudiante' => [
                'id' => $application->student?->id,
                'nombre' => $this->fullName($application->student?->user),
                'carrera' => $application->student?->career?->nombre,
            ],
            'oferta' => [
                'id' => $application->offer?->id,
                'titulo' => $application->offer?->titulo,
                'empresa' => $application->offer?->company?->razon_social ?? $application->offer?->empresa,
            ],
        ];
    }

    /**
     * Misma fórmula que la postulación web: carrera 40 + nivel 25 + habilidades 35.
     */
    private function matchingScore(Student $student, Offer $offer): float
    {
        $offerSkills = collect($offer->habilidades ?? [])->map(fn (string $s) => mb_strtolower(trim($s)))->unique();
        $studentSkills = collect($student->habilidades ?? [])->map(fn (string $s) => mb_strtolower(trim($s)))->unique();
        $careerScore = $offer->careers()->whereKey($student->career_id)->exists() ? 40 : 0;
        $levelScore = mb_strtolower((string) $student->nivel_avance) === mb_strtolower($offer->nivel_requerido ?? '') ? 25 : 0;
        $skillScore = $offerSkills->isEmpty() ? 35 : 35 * $offerSkills->intersect($studentSkills)->count() / $offerSkills->count();

        return round($careerScore + $levelScore + $skillScore, 2);
    }
}
