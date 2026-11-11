<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Company;
use App\Models\Practice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Área de la empresa: sus postulantes y sus practicantes.
 */
class CompanyPortalController extends Controller
{
    public function applicants(Request $request): View
    {
        $companyIds = $this->companyIds($request);

        $applications = Application::query()
            ->with(['student.career:id,codigo,nombre', 'student.user:id,name,last_name', 'offer:id,titulo,empresa_id'])
            ->whereHas('offer', fn ($offer) => $offer->whereIn('empresa_id', $companyIds))
            ->orderByDesc('matching_score')
            ->get();

        return view('company.applicants', compact('applications'));
    }

    public function practices(Request $request): View
    {
        $companyIds = $this->companyIds($request);

        $practices = Practice::query()
            ->with(['application.student.user:id,name,last_name', 'application.student.career:id,codigo,nombre', 'application.offer:id,titulo,empresa_id', 'evaluations'])
            ->whereHas('application.offer', fn ($offer) => $offer->whereIn('empresa_id', $companyIds))
            ->latest()
            ->get();

        return view('company.practices', compact('practices'));
    }

    /**
     * @return list<int>
     */
    private function companyIds(Request $request): array
    {
        $user = $request->user();

        return $user->hasAnyRole(['Administrador', 'Super Administrador'])
            ? Company::pluck('id')->all()
            : Company::where('owner_user_id', $user->id)->pluck('id')->all();
    }
}
