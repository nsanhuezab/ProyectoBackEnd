<?php

namespace App\Http\Controllers;

use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $practices = $this->practices();
        $finalized = $practices->where('estado', 'Finalizada');
        $passed = $finalized->filter(fn (Practice $practice) => (float) $practice->calificacion_final >= 4);
        $employed = $finalized->where('contratado', true);
        $byCareer = $this->countBy($practices, fn (Practice $practice) => $practice->application?->student?->career?->nombre ?? 'Sin carrera');
        $byCompany = $this->countBy($practices, fn (Practice $practice) => $practice->application?->offer?->company?->razon_social ?? 'Sin empresa');
        $byModality = $this->countBy($practices, fn (Practice $practice) => $practice->application?->offer?->modalidad ?? 'Sin modalidad');
        $bySector = $this->countBy($practices, fn (Practice $practice) => $practice->application?->offer?->company?->rubro ?? 'Sin rubro');
        $companyRatings = $practices
            ->flatMap(fn (Practice $practice) => $practice->evaluations
                ->where('tipo', 'estudiante')
                ->map(fn ($evaluation) => [
                    'company' => $practice->application?->offer?->company?->razon_social ?? 'Sin empresa',
                    'rating' => (float) $evaluation->calificacion,
                ]))
            ->groupBy('company')
            ->map(fn (Collection $ratings, string $company) => [
                'label' => $company,
                'average' => round($ratings->avg('rating'), 2),
                'count' => $ratings->count(),
            ])
            ->sortByDesc('average')
            ->values();

        return view('reports.index', [
            'total' => $practices->count(),
            'active' => $practices->whereIn('estado', ['Asignado', 'En Curso', 'Informe Entregado'])->count(),
            'approvalRate' => $finalized->isEmpty() ? 0 : round($passed->count() / $finalized->count() * 100, 1),
            'employmentRate' => $finalized->isEmpty() ? 0 : round($employed->count() / $finalized->count() * 100, 1),
            'byCareer' => $byCareer,
            'byCompany' => $byCompany,
            'byModality' => $byModality,
            'bySector' => $bySector,
            'companyRatings' => $companyRatings,
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $practices = $this->practices();

        return response()->streamDownload(function () use ($practices): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            foreach ($this->exportRows($practices) as $row) {
                fputcsv($output, $row, ';', '"', '\\', "\n");
            }

            fclose($output);
        }, 'reporte-practicas.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportExcel(): StreamedResponse
    {
        $rows = $this->exportRows($this->practices());

        return response()->streamDownload(function () use ($rows): void {
            $temporaryFile = tempnam(sys_get_temp_dir(), 'practice-report-');
            abort_unless($temporaryFile !== false, 500, 'No se pudo crear el reporte temporal.');
            $archive = new \ZipArchive();
            abort_unless($archive->open($temporaryFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true, 500, 'No se pudo generar el archivo Excel.');

            $archive->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
            $archive->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
            $archive->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Practicas" sheetId="1" r:id="rId1"/></sheets></workbook>');
            $archive->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
            $archive->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml($rows));
            $archive->close();
            readfile($temporaryFile);
            unlink($temporaryFile);
        }, 'reporte-practicas.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function acta(Request $request, Practice $practice): View
    {
        $practice->load([
            'application.student.user',
            'application.student.career',
            'application.offer.company',
            'evaluations.evaluator',
        ]);
        $user = $request->user();
        $isStudent = $user->hasRole('Estudiante') && $practice->application?->student?->user_id === $user->id;
        $isCompany = $user->hasRole('Empresa') && $practice->application?->offer?->company?->owner_user_id === $user->id;
        $isCoordinator = $user->hasRole('Coordinador') && $practice->application?->student?->career_id === $user->career_id;

        abort_unless($isStudent || $isCompany || $isCoordinator || $user->hasAnyRole(['Administrador', 'Super Administrador']), 403);
        abort_unless($practice->estado === 'Finalizada', 409, 'El acta estará disponible cuando la práctica haya finalizado.');

        return view('reports.acta', [
            'practice' => $practice,
            'competencies' => $practice->evaluations->flatMap(fn ($evaluation) => $evaluation->competencias ?? [])->unique()->values(),
        ]);
    }

    /** @return Collection<int, Practice> */
    private function practices(): Collection
    {
        return Practice::query()->with([
            'application.student.user',
            'application.student.career',
            'application.offer.company',
            'evaluations',
        ])->orderByDesc('id')->get();
    }

    /** @param Collection<int, Practice> $practices */
    private function countBy(Collection $practices, callable $label): Collection
    {
        return $practices->groupBy($label)
            ->map(fn (Collection $items, string $name) => ['label' => $name, 'count' => $items->count()])
            ->sortByDesc('count')
            ->values();
    }

    /** @param Collection<int, Practice> $practices
     *  @return array<int, array<int, int|float|string|null>>
     */
    private function exportRows(Collection $practices): array
    {
        $rows = [[
            'Estudiante', 'Carrera', 'Empresa', 'Rubro', 'Oferta', 'Modalidad', 'Estado',
            'Fecha inicio', 'Fecha termino', 'Calificacion final', 'Contratado',
        ]];

        foreach ($practices as $practice) {
            $student = $practice->application?->student;
            $offer = $practice->application?->offer;
            $company = $offer?->company;
            $rows[] = [
                trim(($student?->user?->name ?? '').' '.($student?->user?->last_name ?? '')),
                $student?->career?->nombre ?? $student?->carrera,
                $company?->razon_social ?? $offer?->empresa,
                $company?->rubro,
                $offer?->titulo,
                $offer?->modalidad,
                $practice->estado,
                $practice->fecha_inicio?->toDateString(),
                $practice->fecha_termino?->toDateString(),
                $practice->calificacion_final,
                $practice->contratado ? 'Si' : 'No',
            ];
        }

        return $rows;
    }

    /** @param array<int, array<int, int|float|string|null>> $rows */
    private function worksheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $xml .= '<row r="'.$excelRow.'">';
            foreach ($row as $columnIndex => $value) {
                $cell = chr(65 + $columnIndex).$excelRow;
                if (is_numeric($value) && $value !== '') {
                    $xml .= '<c r="'.$cell.'"><v>'.$value.'</v></c>';
                    continue;
                }
                $text = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $xml .= '<c r="'.$cell.'" t="inlineStr"><is><t xml:space="preserve">'.$text.'</t></is></c>';
            }
            $xml .= '</row>';
        }

        return $xml.'</sheetData></worksheet>';
    }
}
