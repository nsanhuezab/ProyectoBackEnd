<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    use HasDataTable;

    public function index(Request $request): JsonResponse
    {
        return $this->dataTableResponse(
            $request,
            Company::query(),
            searchable: ['razon_social', 'rut', 'rubro', 'contacto_nombre', 'contacto_correo'],
            sortable: [
                'id' => 'id',
                'razon_social' => 'razon_social',
                'rut' => 'rut',
                'rubro' => 'rubro',
            ],
            transform: fn (Company $company) => $company->only([
                'id', 'razon_social', 'rut', 'rubro', 'direccion', 'sitio_web',
                'contacto_nombre', 'contacto_correo', 'contacto_telefono',
            ]),
            defaultSort: 'razon_social',
        );
    }

    public function store(CompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());

        return response()->json($company, 201);
    }

    public function update(CompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return response()->json($company->refresh());
    }

    public function destroy(Company $company): Response
    {
        abort_if($company->offers()->exists(), 409, 'No puedes eliminar una empresa con ofertas registradas.');
        $company->delete();

        return response()->noContent();
    }
}
