<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Offer;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::where('id_student', 20261001)->firstOrFail();
        $cvPath = 'cvs/demo/demo-resume.pdf';
        Storage::disk('local')->put($cvPath, $this->demoPdf());

        foreach (Offer::orderBy('id')->get() as $index => $offer) {
            Application::updateOrCreate(
                ['student_id' => $student->id, 'offer_id' => $offer->id],
                [
                    'status' => 'Postulado',
                    'cv_path' => $cvPath,
                    'motivation' => 'Me interesa aportar mis habilidades al equipo y seguir desarrollándome profesionalmente.',
                    'matching_score' => $index === 0 ? 91.25 : 52.5,
                ],
            );
        }

        // Segunda estudiante: postula al Analista de Datos (su práctica ya terminó, ver PracticeSeeder).
        $maria = Student::where('id_student', 20261002)->first();
        $dataOffer = Offer::where('titulo', 'Analista de Datos Jr')->first();
        if ($maria && $dataOffer) {
            Application::updateOrCreate(
                ['student_id' => $maria->id, 'offer_id' => $dataOffer->id],
                [
                    'status' => 'Postulado',
                    'cv_path' => $cvPath,
                    'motivation' => 'Quiero aplicar mis conocimientos de análisis de datos en el sector financiero.',
                    'matching_score' => 89.0,
                ],
            );
        }
    }

    private function demoPdf(): string
    {
        $stream = 'BT /F1 18 Tf 72 720 Td (CV de demostracion) Tj ET';
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";
    }
}
