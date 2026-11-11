<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Oferta de práctica publicada por una empresa.
 */
class Offer extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'titulo',
        'empresa',
        'empresa_id',
        'created_by',
        'approved_by',
        'vacantes',
        'estado',
        'area',
        'perfil',
        'nivel_requerido',
        'min_gpa',
        'modalidad',
        'duracion_semanas',
        'horario',
        'remuneracion',
        'habilidades',
        'approved_at',
        'ubicacion',
    ];

    protected function casts(): array
    {
        return [
            'habilidades' => 'array',
            'approved_at' => 'datetime',
            'remuneracion' => 'decimal:2',
            'min_gpa' => 'decimal:1',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activityLabel(): string
    {
        return 'Oferta «'.$this->titulo.'»';
    }
}
