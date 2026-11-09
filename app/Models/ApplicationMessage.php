<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mensaje dentro de una postulación (RF-03). Guarda el estado de la postulación al momento de escribirlo.
 */
class ApplicationMessage extends Model
{
    protected $fillable = ['application_id', 'user_id', 'application_status', 'body'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
