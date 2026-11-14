<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Documentación pública de la API REST (vista /docs/api).
 * Lee docs/api/endpoints.json, generado con docs/api/generate_assets.py.
 */
class ApiDocsController extends Controller
{
    public function index(): View
    {
        $spec = json_decode(file_get_contents(base_path('docs/api/endpoints.json')), true);
        $baseUrl = url('/api/v1');

        return view('docs.api', [
            'info' => $spec['info'],
            'groups' => $spec['groups'],
            'baseUrl' => $baseUrl,
        ]);
    }

    public function postman(string $file): BinaryFileResponse
    {
        $files = [
            'collection' => 'ProyectoBackEnd.postman_collection.json',
            'environment' => 'ProyectoBackEnd.postman_environment.json',
        ];
        abort_unless(isset($files[$file]), 404);

        return response()->download(base_path('docs/postman/'.$files[$file]));
    }
}
