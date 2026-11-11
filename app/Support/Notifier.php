<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\PlatformNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Envía las notificaciones de la plataforma.
 */
class Notifier
{
    /**
     * @param  User|iterable<User>|null  $users
     */
    public static function send(User|iterable|null $users, string $title, string $message, ?string $url = null): void
    {
        $recipients = Collection::wrap($users instanceof User ? [$users] : ($users ?? []))
            ->filter(fn ($user) => $user instanceof User && $user->exists)
            ->reject(fn (User $user) => $user->id === Auth::id()) // no se avisa a quien hizo la acción
            ->unique('id');

        if ($recipients->isEmpty()) {
            return;
        }

        $notification = new PlatformNotification($title, $message, $url);

        // Aviso en la campana
        Notification::sendNow($recipients, $notification, ['database']);

        // Copia por correo. Si falla, la acción del usuario no se interrumpe.
        if (config('practices.mail_notifications')) {
            try {
                Notification::sendNow($recipients, $notification, ['mail']);
            } catch (Throwable $exception) {
                Log::warning('No se pudo enviar el correo de notificación: '.$exception->getMessage());
            }
        }
    }

    /**
     * Coordinadores a cargo de alguna de las carreras indicadas.
     *
     * @param  iterable<int>  $careerIds
     * @return Collection<int, User>
     */
    public static function coordinatorsOf(iterable $careerIds): Collection
    {
        $ids = collect($careerIds)->filter()->all();

        return $ids === []
            ? collect()
            : User::whereHas('roles', fn ($roles) => $roles->where('name', 'Coordinador'))->whereIn('career_id', $ids)->get();
    }
}
