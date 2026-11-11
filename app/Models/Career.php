<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Carrera con los porcentajes para ponderar la nota final de la práctica.
 */
class Career extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'nombre',
        'codigo',
        'supervisor',
        'coordinador',
        'activa'
    ];

    protected function casts(): array
    {
        return [
            'supervisor' => 'float',
            'coordinador' => 'float',
            'activa' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class);
    }

    public function activityLabel(): string
    {
        return 'Carrera «'.$this->nombre.'»';
    }
}
