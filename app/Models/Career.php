<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
    use HasFactory;

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

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class);
    }
}
