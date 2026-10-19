<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rut', 'role', 'telefono', 'activo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = [
        'administrador' => 'Administrador',
        'coordinador' => 'Coordinador',
        'estudiante' => 'Estudiante',
        'empresa' => 'Empresa',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }

    public function hasRole(string ...$roles): bool
    {
        return $this->isAdmin() || in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst((string) $this->role);
    }

    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class);
    }

    public function empresa(): HasOne
    {
        return $this->hasOne(Empresa::class);
    }
}
