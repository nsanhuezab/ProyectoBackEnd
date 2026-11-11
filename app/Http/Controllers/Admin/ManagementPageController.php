<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

/**
 * Páginas del área de estudiante.
 */
class ManagementPageController extends Controller
{
    public function studentOffers(): View
    {
        return view('student.offers', [
            'apiUrl' => route('student.offers.data'),
            'applyUrl' => route('student.applications.store'),
            'careers' => Career::query()->where('activa', true)->orderBy('nombre')->pluck('nombre', 'id'),
        ]);
    }

    /**
     * "Mis postulaciones": postulaciones del estudiante que inició sesión.
     */
    public function studentApplications(Request $request): View
    {
        $applications = Application::query()
            ->with(['offer:id,titulo,empresa,empresa_id,modalidad', 'offer.company:id,razon_social', 'messages:id,application_id'])
            ->whereHas('student', fn ($student) => $student->where('user_id', $request->user()->id))
            ->latest()
            ->get();

        return view('student.applications', compact('applications'));
    }
}
