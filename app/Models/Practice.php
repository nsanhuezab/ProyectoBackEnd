<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Practice extends Model
{
    protected $fillable = [
        'application_id',
        'assigned_by',
        'fecha_inicio',
        'fecha_termino',
        'estado',
        'calificacion_final',
        'contratado',
        'finalizada_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_termino' => 'date',
            'finalizada_at' => 'datetime',
            'calificacion_final' => 'decimal:2',
            'contratado' => 'boolean',
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
        return $this->hasMany(PracticeLog::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function calculateFinalGrade(): ?float
    {
        $this->loadMissing(['application.student.career', 'evaluations']);
        $career = $this->application?->student?->career;
        $supervisorGrade = $this->evaluations->firstWhere('tipo', 'supervisor')?->calificacion;
        $coordinatorGrade = $this->evaluations->firstWhere('tipo', 'coordinador')?->calificacion;

        if (! $career || $supervisorGrade === null || $coordinatorGrade === null) {
            return null;
        }

        return round(
            ((float) $supervisorGrade * (float) $career->supervisor
                + (float) $coordinatorGrade * (float) $career->coordinador) / 100,
            2
        );
    }
}
