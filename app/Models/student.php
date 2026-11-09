<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'id_student',
        'user_id',
        'career_id',
        'carrera',
        'nivel_avance',
        'habilidades',
        'year_level',
        'gpa',
        'validated_at',
        'validated_by',
    ];

    protected function casts(): array
    {
        return [
            'habilidades' => 'array',
            'gpa' => 'decimal:1',
            'year_level' => 'integer',
            'validated_at' => 'datetime',
        ];
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

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function activityLabel(): string
    {
        return 'Estudiante #'.$this->id_student;
    }
}
