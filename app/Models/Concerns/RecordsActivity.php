<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Guarda en activity_logs quién creó, modificó o eliminó un registro y qué cambió.
 */
trait RecordsActivity
{
    /**
     * Campos que nunca se guardan en el historial.
     *
     * @var list<string>
     */
    protected static array $activityHiddenFields = ['password', 'remember_token', 'updated_at', 'created_at'];

    public static function bootRecordsActivity(): void
    {
        static::created(fn (Model $model) => static::recordActivity($model, 'created', $model->getAttributes()));

        static::updated(function (Model $model): void {
            $changes = [];
            foreach ($model->getChanges() as $field => $value) {
                $changes[$field] = ['antes' => $model->getOriginal($field), 'después' => $value];
            }
            static::recordActivity($model, 'updated', $changes);
        });

        static::deleted(fn (Model $model) => static::recordActivity($model, 'deleted', $model->getAttributes()));
    }

    /**
     * Nombre del registro que se muestra en el historial.
     */
    public function activityLabel(): string
    {
        return class_basename($this).' #'.$this->getKey();
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    protected static function recordActivity(Model $model, string $action, array $changes): void
    {
        $changes = collect($changes)->except(static::$activityHiddenFields)->all();
        if ($action === 'updated' && $changes === []) {
            return;
        }

        $userId = Auth::id();

        ActivityLog::create([
            'user_id' => $userId > 0 ? $userId : null,
            'action' => $action,
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'description' => $model->activityLabel(),
            'changes' => $changes,
        ]);
    }
}
