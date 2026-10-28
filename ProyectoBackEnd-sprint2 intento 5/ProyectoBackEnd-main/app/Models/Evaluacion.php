<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    public const TIPOS = [
        'supervisor' => 'Supervisor de empresa',
        'coordinador' => 'Coordinador',
        'informe' => 'Informe final',
    ];

    protected $table = 'evaluaciones';

    protected $fillable = ['practica_id', 'evaluador_id', 'tipo', 'nota', 'fecha', 'comentario'];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'nota' => 'decimal:1',
        ];
    }

    public function practica(): BelongsTo
    {
        return $this->belongsTo(Practica::class);
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }
}
