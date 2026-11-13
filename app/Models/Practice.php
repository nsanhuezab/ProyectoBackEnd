<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Práctica creada al asignar una postulación.
 */
class Practice extends Model
{
    use RecordsActivity;

    protected $fillable = [
        'application_id',
        'assigned_by',
        'fecha_inicio',
        'fecha_termino',
        'estado',
        'final_grade',
        'hired',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_termino' => 'date',
            'final_grade' => 'decimal:1',
            'hired' => 'boolean',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PracticeLog::class)->orderBy('period_start');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PracticeEvaluation::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(CompanyReview::class);
    }

    /**
     * Rúbrica de un evaluador: 'supervisor' o 'coordinador'.
     */
    public function evaluationBy(string $role): ?PracticeEvaluation
    {
        return $this->evaluations->firstWhere('evaluator_role', $role);
    }

    /**
     * Nota final ponderada con los porcentajes de la carrera.
     * Es null mientras falte la nota del supervisor o la del coordinador.
     */
    public function calculateFinalGrade(): ?float
    {
        $this->loadMissing(['evaluations', 'application.student.career']);
        $supervisor = $this->evaluationBy(PracticeEvaluation::SUPERVISOR);
        $coordinator = $this->evaluationBy(PracticeEvaluation::COORDINATOR);
        $career = $this->application?->student?->career;

        if (! $supervisor || ! $coordinator || ! $career) {
            return null;
        }

        $grade = (float) $supervisor->grade * (float) $career->supervisor / 100
            + (float) $coordinator->grade * (float) $career->coordinador / 100;

        return round($grade, 1);
    }

    /**
     * true si aprobó, false si reprobó y null si aún no tiene nota.
     */
    public function isApproved(): ?bool
    {
        return $this->final_grade === null ? null : (float) $this->final_grade >= config('practices.passing_grade');
    }

    public function activityLabel(): string
    {
        return 'Práctica #'.$this->id;
    }
}
