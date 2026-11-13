<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro quincenal de avance (bitácora) de una práctica.
 */
class PracticeLog extends Model
{
    use RecordsActivity;

    protected $fillable = ['practice_id', 'period_start', 'period_end', 'hours', 'activities', 'learnings', 'difficulties'];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'hours' => 'integer',
        ];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function activityLabel(): string
    {
        return 'Bitácora '.$this->period_start?->format('d-m-Y').' a '.$this->period_end?->format('d-m-Y');
    }
}
