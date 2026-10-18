<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = [
        'practice_id',
        'evaluator_id',
        'tipo',
        'calificacion',
        'competencias',
        'comentarios',
    ];

    protected function casts(): array
    {
        return [
            'calificacion' => 'decimal:2',
            'competencias' => 'array',
        ];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
