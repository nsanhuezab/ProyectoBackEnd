<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Campana de notificaciones (RF-10).
 */
class NotificationController extends Controller
{
    /**
     * Marca la notificación como leída y lleva a la página relacionada.
     */
    public function open(Request $request, string $notification): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        return $notification->data['url'] ?? null
            ? redirect()->to($notification->data['url'])
            : back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Notificaciones marcadas como leídas.');
    }
}
