<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    public const ESTADOS = ['activa' => 'Activa', 'inactiva' => 'Inactiva'];

    protected $fillable = [
        'user_id', 'razon_social', 'rut', 'giro', 'contacto_nombre',
        'email', 'telefono', 'direccion', 'estado',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }

    public function practicas(): HasMany
    {
        return $this->hasMany(Practica::class);
    }
}
