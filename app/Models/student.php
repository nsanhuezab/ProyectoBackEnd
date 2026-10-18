<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_student',
        'user_id',
        'career_id',
        'carrera',
        'nivel_avance',
        'habilidades',
    ];

    protected function casts(): array
    {
        return ['habilidades' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
