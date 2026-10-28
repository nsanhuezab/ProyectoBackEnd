<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Oferta extends Model
{
    public const ESTADOS = ['abierta' => 'Abierta', 'cerrada' => 'Cerrada', 'borrador' => 'Borrador'];

    public const MODALIDADES = ['presencial' => 'Presencial', 'remota' => 'Remota', 'hibrida' => 'Híbrida'];

    protected $fillable = [
        'empresa_id', 'titulo', 'area', 'descripcion', 'requisitos',
        'modalidad', 'cupos', 'horas', 'fecha_limite', 'estado',
    ];

    protected function casts(): array
    {
        return ['fecha_limite' => 'date'];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class);
    }

    /** Ofertas visibles para los estudiantes. */
    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->where('estado', 'abierta')
            ->where(fn ($q) => $q->whereNull('fecha_limite')->orWhere('fecha_limite', '>=', now()->toDateString()));
    }
}
