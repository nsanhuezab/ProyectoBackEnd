<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $fillable = ['user_id', 'carrera_id', 'matricula', 'semestre', 'telefono'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class);
    }

    public function practicas(): HasMany
    {
        return $this->hasMany(Practica::class);
    }
}
