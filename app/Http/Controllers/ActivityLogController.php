<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;

/**
 * Historial de cambios del sistema (RF-11). Solo lectura.
 */
class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::query()
            ->with('user:id,name,last_name,email')
            ->latest('created_at')
            ->latest('id')
            ->limit(1000)
            ->get();

        return view('activity.index', compact('logs'));
    }
}
