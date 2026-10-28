<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Career;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ManagementPageController extends Controller
{
    public function index(Request $request, string $resource): View
    {
        abort_unless(in_array($resource, [
            'careers', 'coordinators', 'students', 'companies', 'offers', 'applications', 'practices',
        ], true), 404);
        if ($request->user()?->hasRole('Coordinador')) {
            abort_unless(in_array($resource, ['offers', 'applications', 'practices'], true), 403);
        }

        $careerOptions = Career::query()->orderBy('nombre')->pluck('nombre', 'id');
        $companyOptions = Company::query()->orderBy('razon_social')->pluck('razon_social', 'id');
        $companyUsers = User::role('Empresa')->orderBy('name')->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (User $user) => [$user->id => $user->name.' ('.$user->email.')']);
        $studentUsers = User::role('Estudiante')->orderBy('name')->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (User $user) => [$user->id => $user->name.' ('.$user->email.')']);
        $pendingApplicationsQuery = Application::query()
            ->with(['student.user:id,name,last_name', 'offer:id,titulo'])
            ->where('status', 'Postulado');
        if ($request->user()?->hasRole('Coordinador')) {
            $pendingApplicationsQuery->whereHas('student', fn ($student) => $student->where('career_id', $request->user()->career_id));
        }
        $pendingApplications = $pendingApplicationsQuery->get()->mapWithKeys(fn (Application $application) => [
            $application->id => '#'.$application->id.' · '.trim(($application->student?->user?->name ?? '').' '.($application->student?->user?->last_name ?? '')).' · '.($application->offer?->titulo ?? ''),
        ]);

        $config = match ($resource) {
            'careers' => [
                'title' => 'Carreras',
                'description' => 'Gestiona carreras y ponderaciones de evaluación.',
                'columns' => $this->columns(['codigo' => 'Código', 'nombre' => 'Carrera', 'supervisor' => 'Supervisor %', 'coordinador' => 'Coordinador %', 'activa' => 'Estado']),
                'fields' => [
                    $this->field('codigo', 'Código'), $this->field('nombre', 'Nombre'),
                    $this->field('supervisor', 'Ponderación supervisor (%)', 'number', ['min' => 0, 'max' => 100, 'step' => '0.01']),
                    $this->field('coordinador', 'Ponderación coordinador (%)', 'number', ['min' => 0, 'max' => 100, 'step' => '0.01']),
                    $this->field('activa', 'Activa', 'checkbox'),
                ],
            ],
            'coordinators' => [
                'title' => 'Coordinadores',
                'description' => 'Administra coordinadores y su carrera asignada.',
                'columns' => $this->columns(['nombre' => 'Nombre', 'correo' => 'Correo', 'telefono' => 'Teléfono', 'carrera' => 'Carrera']),
                'fields' => [
                    $this->field('nombre', 'Nombre'), $this->field('correo', 'Correo', 'email'),
                    $this->field('telefono', 'Teléfono'), $this->selectField('career_id', 'Carrera', $careerOptions),
                ],
            ],
            'students' => [
                'title' => 'Estudiantes',
                'description' => 'Vincula cuentas de estudiante con carrera, avance y habilidades.',
                'columns' => $this->columns(['id_student' => 'ID estudiante', 'estudiante' => 'Nombre', 'correo' => 'Correo', 'carrera' => 'Carrera', 'nivel_avance' => 'Nivel', 'habilidades' => 'Habilidades']),
                'fields' => [
                    $this->field('id_student', 'ID estudiante', 'number', ['min' => 1]),
                    $this->selectField('user_id', 'Cuenta de estudiante', $studentUsers),
                    $this->selectField('career_id', 'Carrera', $careerOptions),
                    $this->field('nivel_avance', 'Nivel de avance'), $this->field('habilidades', 'Habilidades', 'tags'),
                ],
            ],
            'companies' => [
                'title' => 'Empresas',
                'description' => 'Mantén el registro institucional y el contacto supervisor.',
                'columns' => $this->columns(['razon_social' => 'Razón social', 'rut' => 'RUT', 'rubro' => 'Rubro', 'contacto_nombre' => 'Contacto', 'contacto_email' => 'Correo', 'telefono' => 'Teléfono']),
                'fields' => [
                    $this->selectField('owner_user_id', 'Cuenta de empresa', $companyUsers, false),
                    $this->field('razon_social', 'Razón social'), $this->field('rut', 'RUT'),
                    $this->field('rubro', 'Rubro'), $this->field('direccion', 'Dirección'),
                    $this->field('sitio_web', 'Sitio web', 'url'), $this->field('contacto_nombre', 'Supervisor de práctica'),
                    $this->field('contacto_email', 'Correo del supervisor', 'email'), $this->field('telefono', 'Teléfono del supervisor'),
                ],
            ],
            'offers' => [
                'title' => 'Ofertas de práctica',
                'description' => 'Administra ofertas y aprueba su publicación para estudiantes.',
                'columns' => $this->columns(['titulo' => 'Oferta', 'empresa' => 'Empresa', 'carreras' => 'Carreras', 'area' => 'Área', 'modalidad' => 'Modalidad', 'vacantes' => 'Cupos', 'estado' => 'Estado']),
                'fields' => [
                    $this->field('titulo', 'Título'), $this->selectField('empresa_id', 'Empresa', $companyOptions),
                    $this->selectField('career_ids', 'Carreras requeridas', $careerOptions, true),
                    $this->field('area', 'Área'), $this->field('perfil', 'Perfil requerido', 'textarea'),
                    $this->field('nivel_requerido', 'Nivel requerido'),
                    $this->selectField('modalidad', 'Modalidad', collect(['Presencial', 'Remota', 'Híbrida'])->mapWithKeys(fn ($item) => [$item => $item])),
                    $this->field('duracion_semanas', 'Duración (semanas)', 'number', ['min' => 1, 'max' => 104]),
                    $this->field('horario', 'Horario'), $this->field('remuneracion', 'Remuneración', 'number', ['min' => 0, 'step' => '0.01']),
                    $this->field('vacantes', 'Cupos disponibles', 'number', ['min' => 1]),
                    $this->field('habilidades', 'Habilidades requeridas', 'tags'), $this->field('ubicacion', 'Ubicación'),
                ],
                'actions' => ['approval'],
            ],
            'applications' => [
                'title' => 'Postulaciones',
                'description' => 'Revisa afinidad y decide asignaciones según los cupos disponibles.',
                'columns' => $this->columns(['student' => 'Estudiante', 'career' => 'Carrera', 'offer' => 'Oferta', 'company' => 'Empresa', 'matching_score' => 'Afinidad %', 'estado' => 'Estado', 'created_at' => 'Postulación']),
                'fields' => [
                    $this->selectField('estado', 'Estado', collect(['Postulado', 'Rechazado'])->mapWithKeys(fn ($item) => [$item => $item])),
                ],
                'actions' => ['application'],
                'canCreate' => false,
            ],
            'practices' => [
                'title' => 'Prácticas',
                'description' => 'Asigna postulaciones aprobadas y actualiza el estado de la práctica.',
                'columns' => $this->columns(['estudiante' => 'Estudiante', 'carrera' => 'Carrera', 'oferta' => 'Oferta', 'empresa' => 'Empresa', 'fecha_inicio' => 'Inicio', 'fecha_termino' => 'Término', 'estado' => 'Estado']),
                'fields' => [
                    $this->selectField('application_id', 'Postulación asignada', $pendingApplications),
                    $this->field('fecha_inicio', 'Fecha de inicio', 'date'),
                    $this->field('fecha_termino', 'Fecha de término', 'date'),
                ],
                'actions' => ['transition'],
            ],
        };

        if ($request->user()?->hasRole('Coordinador')) {
            $config['canCreate'] = $resource === 'practices';
            $config['canDelete'] = false;
            if ($resource === 'offers') {
                $config['fields'] = [];
            }
        }

        return view('admin.management', [
            'resource' => $resource,
            'config' => array_merge([
                'apiUrl' => url(($request->user()?->hasRole('Coordinador') ? '/coordinator/' : '/admin/').$resource),
                'dataUrl' => url(($request->user()?->hasRole('Coordinador') ? '/coordinator/' : '/admin/').$resource),
                'canCreate' => true,
                'canDelete' => true,
                'actions' => [],
            ], $config),
        ]);
    }

    public function studentOffers(): View
    {
        return view('student.offers', [
            'apiUrl' => route('student.offers.data'),
            'applyUrl' => route('applications.store'),
            'careers' => Career::query()->where('activa', true)->orderBy('nombre')->pluck('nombre', 'id'),
        ]);
    }

    public function studentApplications(): View
    {
        return view('student.applications', [
            'apiUrl' => route('student.applications.index'),
        ]);
    }

    public function companyOffers(): View
    {
        $company = Company::query()->where('owner_user_id', auth()->id())->firstOrFail();
        $careerOptions = Career::query()->orderBy('nombre')->pluck('nombre', 'id');

        return view('admin.management', [
            'resource' => 'offers',
            'config' => [
                'apiUrl' => url('/company/offers'),
                'dataUrl' => route('company.offers.data'),
                'title' => 'Mis ofertas',
                'description' => 'Publica ofertas y revisa su estado de aprobación.',
                'columns' => $this->columns(['titulo' => 'Oferta', 'carreras' => 'Carreras', 'area' => 'Área', 'modalidad' => 'Modalidad', 'vacantes' => 'Cupos', 'estado' => 'Estado']),
                'fields' => [
                    $this->field('titulo', 'Título'),
                    $this->selectField('career_ids', 'Carreras requeridas', $careerOptions, true),
                    $this->field('area', 'Área'), $this->field('perfil', 'Perfil requerido', 'textarea'),
                    $this->field('nivel_requerido', 'Nivel requerido'),
                    $this->selectField('modalidad', 'Modalidad', collect(['Presencial', 'Remota', 'Híbrida'])->mapWithKeys(fn ($item) => [$item => $item])),
                    $this->field('duracion_semanas', 'Duración (semanas)', 'number', ['min' => 1, 'max' => 104]),
                    $this->field('horario', 'Horario'), $this->field('remuneracion', 'Remuneración', 'number', ['min' => 0, 'step' => '0.01']),
                    $this->field('vacantes', 'Cupos disponibles', 'number', ['min' => 1]),
                    $this->field('habilidades', 'Habilidades requeridas', 'tags'), $this->field('ubicacion', 'Ubicación'),
                ],
                'defaults' => ['empresa_id' => $company->id],
                'canCreate' => true,
                'actions' => [],
            ],
        ]);
    }

    /** @param array<string, string> $columns */
    private function columns(array $columns): array
    {
        return collect($columns)->map(fn (string $title, string $data) => compact('data', 'title'))->values()->all();
    }

    /** @param array<string, int|float|string> $attributes */
    private function field(string $name, string $label, string $type = 'text', array $attributes = []): array
    {
        return compact('name', 'label', 'type', 'attributes');
    }

    /** @param Collection<int|string, string> $options */
    private function selectField(string $name, string $label, Collection $options, bool $multiple = false): array
    {
        return [
            'name' => $name,
            'label' => $label,
            'type' => 'select',
            'multiple' => $multiple,
            'options' => $options,
        ];
    }
}
