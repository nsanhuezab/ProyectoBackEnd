<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Practice;
use App\Models\PracticeEvaluation;
use App\Models\User;

/**
 * Reglas de quién puede ver y evaluar una práctica.
 */
trait AuthorizesPractices
{
    protected function isAdmin(?User $user): bool
    {
        return (bool) $user?->hasAnyRole(['Administrador', 'Super Administrador']);
    }

    protected function isCareerCoordinator(?User $user, Practice $practice): bool
    {
        return (bool) $user?->hasRole('Coordinador')
            && $user->career_id !== null
            && $practice->application?->student?->career_id === $user->career_id;
    }

    protected function isCompanySupervisor(?User $user, Practice $practice): bool
    {
        return (bool) $user?->hasRole('Empresa')
            && $practice->application?->offer?->company?->owner_user_id === $user->id;
    }

    protected function isPracticeStudent(?User $user, Practice $practice): bool
    {
        return $user !== null && $practice->application?->student?->user_id === $user->id;
    }

    protected function canViewPractice(?User $user, Practice $practice): bool
    {
        return $this->isAdmin($user)
            || $this->isCareerCoordinator($user, $practice)
            || $this->isCompanySupervisor($user, $practice)
            || $this->isPracticeStudent($user, $practice);
    }

    /**
     * El supervisor evalúa como "supervisor"; el coordinador como "coordinador".
     * El administrador puede registrar ambas (por ejemplo, si el supervisor la envía en papel).
     */
    protected function canEvaluate(?User $user, Practice $practice, string $role): bool
    {
        if (! in_array($practice->estado, ['Informe Entregado', 'Evaluado'], true)) {
            return false;
        }

        return $this->isAdmin($user) || match ($role) {
            PracticeEvaluation::SUPERVISOR => $this->isCompanySupervisor($user, $practice),
            PracticeEvaluation::COORDINATOR => $this->isCareerCoordinator($user, $practice),
            default => false,
        };
    }

    protected function loadPracticeRelations(Practice $practice): Practice
    {
        return $practice->loadMissing([
            'application.student.user:id,name,last_name,email,rut',
            'application.student.career',
            'application.offer.company',
            'evaluations.evaluator:id,name,last_name',
            'logs',
            'review',
        ]);
    }
}
