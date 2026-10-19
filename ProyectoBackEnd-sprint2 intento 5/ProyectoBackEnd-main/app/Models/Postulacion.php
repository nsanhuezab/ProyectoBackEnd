<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Postulacion extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'retirada' => 'Retirada',
    ];

    protected $table = 'postulaciones';

    protected $fillable = ['oferta_id', 'estudiante_id', 'mensaje', 'estado'];

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }
}
