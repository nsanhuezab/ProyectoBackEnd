<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Practica extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_curso' => 'En curso',
        'finalizada' => 'Finalizada',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable = [
        'estudiante_id', 'empresa_id', 'oferta_id', 'supervisor',
        'fecha_inicio', 'fecha_termino', 'horas_totales', 'estado', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_termino' => 'date',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    /** Avance (0-100) según las fechas de la práctica. */
    public function progreso(): int
    {
        if (! $this->fecha_inicio || ! $this->fecha_termino || $this->fecha_termino->lte($this->fecha_inicio)) {
            return 0;
        }

        if (now()->lte($this->fecha_inicio)) {
            return 0;
        }

        if (now()->gte($this->fecha_termino)) {
            return 100;
        }

        $total = $this->fecha_inicio->diffInSeconds($this->fecha_termino);
        $done = $this->fecha_inicio->diffInSeconds(now());

        return (int) round($done / $total * 100);
    }
}
