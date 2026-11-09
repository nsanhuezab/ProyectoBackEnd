<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro del historial de cambios (RF-11). Solo se crea; no se edita ni se elimina.
 */
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'description', 'changes'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nombre del módulo en español para mostrarlo en la tabla.
     */
    public function moduleName(): string
    {
        return [
            Career::class => 'Carrera',
            Company::class => 'Empresa',
            Offer::class => 'Oferta',
            Student::class => 'Estudiante',
            Application::class => 'Postulación',
            Practice::class => 'Práctica',
            PracticeEvaluation::class => 'Evaluación',
            PracticeLog::class => 'Bitácora',
            CompanyReview::class => 'Evaluación de empresa',
            User::class => 'Usuario',
        ][$this->subject_type] ?? class_basename($this->subject_type);
    }

    public function actionName(): string
    {
        return ['created' => 'Creó', 'updated' => 'Modificó', 'deleted' => 'Eliminó'][$this->action] ?? $this->action;
    }
}
