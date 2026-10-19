<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    protected $fillable = ['nombre', 'codigo', 'facultad'];

    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }
}
