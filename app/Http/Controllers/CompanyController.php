<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CompanyController extends Controller
{
    /**
     * Mantenedor de empresas en convenio, con su supervisor de práctica y sus ofertas.
     */
    public function index(): View
    {
        $companies = Company::query()
            ->with('owner:id,name,last_name,email')
            ->withCount('offers')
            ->orderBy('razon_social')
            ->get();

        $counts = [
            'total' => $companies->count(),
            'con_ofertas' => $companies->where('offers_count', '>', 0)->count(),
            'con_cuenta' => $companies->whereNotNull('owner_user_id')->count(),
        ];

        $companyUsers = User::role('Empresa')->orderBy('name')->get(['id', 'name', 'last_name', 'email']);

        return view('companies.index', compact('companies', 'counts', 'companyUsers'));
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $company = Company::create($request->validated());

        return back()->with('success', "Empresa «{$company->razon_social}» registrada correctamente.");
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return back()->with('success', "Empresa «{$company->razon_social}» actualizada correctamente.");
    }

    public function destroy(Company $company): RedirectResponse
    {
        $offers = $company->offers()->count();
        if ($offers > 0) {
            return back()->with('error', "No se puede eliminar «{$company->razon_social}» porque tiene {$offers} oferta(s) registradas.");
        }

        $company->delete();

        return back()->with('success', "Empresa «{$company->razon_social}» eliminada correctamente.");
    }
}
