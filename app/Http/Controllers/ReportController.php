<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\CompanyReview;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Panel de indicadores con exportación a PDF y Excel. El coordinador ve solo su carrera.
 */
class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', $this->buildReport($request->user()));
    }

    public function pdf(Request $request): Response
    {
        $data = $this->buildReport($request->user()) + ['generatedAt' => now()];

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', $data)
                ->setPaper('a4')
                ->download('indicadores-practicas-'.now()->format('Y-m-d').'.pdf');
        }

        return response()->view('reports.pdf', $data + ['printable' => true]);
    }

    public function excel(Request $request): StreamedResponse
    {
        $sheets = $this->sheets($this->buildReport($request->user()));
        $fileName = 'indicadores-practicas-'.now()->format('Y-m-d');

        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return response()->streamDownload(function () use ($sheets): void {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
                $spreadsheet->removeSheetByIndex(0);

                foreach ($sheets as $title => $rows) {
                    $sheet = $spreadsheet->createSheet();
                    $sheet->setTitle(mb_substr($title, 0, 31));
                    $sheet->fromArray($rows, null, 'A1');
                    $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);
                    foreach (range('A', $sheet->getHighestColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }

                $spreadsheet->setActiveSheetIndex(0);
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
            }, $fileName.'.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // Respaldo si no está PhpSpreadsheet: CSV separado por ";"
        return response()->streamDownload(function () use ($sheets): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            foreach ($sheets as $title => $rows) {
                fputcsv($output, [mb_strtoupper($title)], ';');
                foreach ($rows as $row) {
                    fputcsv($output, $row, ';');
                }
                fputcsv($output, [], ';');
            }
            fclose($output);
        }, $fileName.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Calcula los indicadores del panel.
     *
     * @return array<string, mixed>
     */
    private function buildReport(?User $user): array
    {
        $careerId = $user?->hasRole('Coordinador') ? $user->career_id : null;
        $activeStates = config('practices.active_states');
        $passing = (float) config('practices.passing_grade');

        $practices = Practice::query()
            ->with(['application.student.career', 'application.offer.company'])
            ->when($careerId, fn ($query) => $query->whereHas('application.student', fn ($student) => $student->where('career_id', $careerId)))
            ->get();

        $active = $practices->whereIn('estado', $activeStates);
        $graded = $practices->whereNotNull('final_grade');
        $finished = $practices->where('estado', 'Finalizada');
        $withHiringInfo = $finished->whereNotNull('hired');

        $careerName = fn (Practice $practice) => $practice->application?->student?->career?->nombre ?? 'Sin carrera';
        $companyName = fn (Practice $practice) => $practice->application?->offer?->company?->razon_social ?? $practice->application?->offer?->empresa ?? 'Sin empresa';

        $reviews = CompanyReview::query()
            ->with('company:id,razon_social,rubro')
            ->when($careerId, fn ($query) => $query->whereHas('practice.application.student', fn ($student) => $student->where('career_id', $careerId)))
            ->get();

        return [
            'scope' => $careerId ? Career::find($careerId)?->nombre : null,
            'totals' => [
                'practicas' => $practices->count(),
                'activas' => $active->count(),
                'finalizadas' => $finished->count(),
                'evaluadas' => $graded->count(),
                'promedio' => $graded->isEmpty() ? null : round($graded->avg(fn (Practice $practice) => (float) $practice->final_grade), 1),
                'aprobacion' => $this->rate($graded->filter(fn (Practice $practice) => (float) $practice->final_grade >= $passing)->count(), $graded->count()),
                'empleabilidad' => $this->rate($withHiringInfo->where('hired', true)->count(), $withHiringInfo->count()),
                'contratados' => $withHiringInfo->where('hired', true)->count(),
            ],
            'byCareer' => $this->countBy($practices, $careerName, $activeStates),
            'byCompany' => $this->countBy($practices, $companyName, $activeStates),
            'byModality' => $this->countBy($practices, fn (Practice $practice) => $practice->application?->offer?->modalidad ?? 'Sin modalidad', $activeStates),
            'byIndustry' => $this->countBy($practices, fn (Practice $practice) => $practice->application?->offer?->company?->rubro ?? 'Sin rubro', $activeStates),
            'ranking' => $reviews->groupBy('company_id')
                ->map(fn (Collection $companyReviews) => [
                    'empresa' => $companyReviews->first()->company?->razon_social,
                    'rubro' => $companyReviews->first()->company?->rubro,
                    'promedio' => round($companyReviews->avg('rating'), 1),
                    'evaluaciones' => $companyReviews->count(),
                ])
                ->sortByDesc('promedio')
                ->values(),
            'passingGrade' => $passing,
        ];
    }

    /**
     * Agrupa las prácticas y cuenta el total y las activas de cada grupo.
     *
     * @return Collection<int, array{nombre: string, total: int, activas: int}>
     */
    private function countBy(Collection $practices, callable $key, array $activeStates): Collection
    {
        return $practices->groupBy($key)
            ->map(fn (Collection $group, string $name) => [
                'nombre' => $name,
                'total' => $group->count(),
                'activas' => $group->whereIn('estado', $activeStates)->count(),
            ])
            ->sortByDesc('total')
            ->values();
    }

    private function rate(int $part, int $total): ?float
    {
        return $total === 0 ? null : round($part / $total * 100, 1);
    }

    /**
     * Filas de cada hoja del Excel.
     *
     * @param  array<string, mixed>  $report
     * @return array<string, list<list<string|int|float|null>>>
     */
    private function sheets(array $report): array
    {
        $totals = $report['totals'];
        $percent = fn (?float $value) => $value === null ? 'Sin datos' : $value.'%';
        $groupRows = fn (Collection $rows) => $rows->map(fn (array $row) => [$row['nombre'], $row['total'], $row['activas']])->all();

        return [
            'Resumen' => [
                ['Indicador', 'Valor'],
                ['Alcance', $report['scope'] ?? 'Todas las carreras'],
                ['Prácticas registradas', $totals['practicas']],
                ['Estudiantes en práctica (activas)', $totals['activas']],
                ['Prácticas finalizadas', $totals['finalizadas']],
                ['Prácticas con nota final', $totals['evaluadas']],
                ['Nota final promedio', $totals['promedio'] ?? 'Sin datos'],
                ['Tasa de aprobación', $percent($totals['aprobacion'])],
                ['Tasa de empleabilidad post-práctica', $percent($totals['empleabilidad'])],
                ['Estudiantes contratados', $totals['contratados']],
            ],
            'Por carrera' => [['Carrera', 'Prácticas', 'En práctica'], ...$groupRows($report['byCareer'])],
            'Por empresa' => [['Empresa', 'Prácticas', 'En práctica'], ...$groupRows($report['byCompany'])],
            'Por modalidad' => [['Modalidad', 'Prácticas', 'En práctica'], ...$groupRows($report['byModality'])],
            'Por rubro' => [['Rubro', 'Prácticas', 'En práctica'], ...$groupRows($report['byIndustry'])],
            'Ranking empresas' => [
                ['Empresa', 'Rubro', 'Promedio (1 a 5)', 'Evaluaciones'],
                ...$report['ranking']->map(fn (array $row) => [$row['empresa'], $row['rubro'], $row['promedio'], $row['evaluaciones']])->all(),
            ],
        ];
    }
}
