<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeLog extends Model
{
    protected $fillable = [
        'practice_id',
        'author_id',
        'fecha',
        'avance',
        'competencias',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'competencias' => 'array',
        ];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
