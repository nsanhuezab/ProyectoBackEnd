<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rúbrica completada por el supervisor de la empresa o por el coordinador académico.
 */
class PracticeEvaluation extends Model
{
    use RecordsActivity;

    public const SUPERVISOR = 'supervisor';

    public const COORDINATOR = 'coordinador';

    protected $fillable = ['practice_id', 'evaluator_role', 'evaluator_id', 'scores', 'grade', 'comments'];

    protected function casts(): array
    {
        return [
            'scores' => 'array',
            'grade' => 'decimal:1',
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

    public function roleLabel(): string
    {
        return $this->evaluator_role === self::SUPERVISOR ? 'Supervisor de la empresa' : 'Coordinador académico';
    }

    public function activityLabel(): string
    {
        return 'Evaluación del '.$this->evaluator_role.' (práctica #'.$this->practice_id.')';
    }
}
