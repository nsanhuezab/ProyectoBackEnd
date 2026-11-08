<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Contracts\View\View;

/**
 * Páginas del área de estudiante. Los mantenedores de administración
 * tienen ahora cada uno su propio controlador y su propia vista.
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

    public function studentApplications(): View
    {
        return view('student.applications', [
            'apiUrl' => route('student.applications.index'),
        ]);
    }
}
