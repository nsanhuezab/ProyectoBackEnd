<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Token Bearer de la API REST privada.
 */
class ApiToken extends Model
{
    protected $fillable = ['user_id', 'name', 'token_hash', 'last_used_at', 'expires_at'];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Crea un token y devuelve [modelo, token en claro]. El claro solo se ve una vez.
     *
     * @return array{0: ApiToken, 1: string}
     */
    public static function issue(User $user, string $name = 'api', int $days = 7): array
    {
        $plain = Str::random(64);

        $token = static::create([
            'user_id' => $user->id,
            'name' => $name,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addDays($days),
        ]);

        return [$token, $plain];
    }
}
