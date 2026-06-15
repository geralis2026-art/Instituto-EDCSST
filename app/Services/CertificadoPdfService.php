<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\ConfiguracionSitio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

/**
 * Genera el PDF del certificado escribiendo los datos del capacitado sobre
 * la plantilla institucional (configuracion_sitio.plantilla_certificado)
 * mediante FPDI. Si no hay plantilla configurada, usa como respaldo la
 * plantilla Blade (resources/views/pdf/certificado.blade.php) con dompdf.
 */
class CertificadoPdfService
{
    /** Renderiza el certificado y devuelve el binario del PDF. */
    public function generarPdf(Certificado $certificado): string
    {
        $certificado->loadMissing(['capacitado', 'curso.categoria']);

        $plantilla = ConfiguracionSitio::obtener()->plantilla_url;

        if ($plantilla && file_exists($plantilla)) {
            return $this->generarConPlantilla($certificado, $plantilla);
        }

        return Pdf::loadView('pdf.certificado', compact('certificado'))
            ->setPaper('letter', 'landscape')
            ->output();
    }

    /**
     * Genera el PDF y lo guarda en el disco "certificados".
     * Devuelve la ruta relativa, lista para asignar a Certificado::archivo_pdf.
     */
    public function generarYGuardar(Certificado $certificado): string
    {
        $pdf = $this->generarPdf($certificado);
        $ruta = "certificados/{$certificado->codigo_unico}.pdf";

        Storage::disk('certificados')->put($ruta, $pdf);

        return $ruta;
    }

    /** Usa la plantilla PDF institucional como fondo y escribe los datos encima. */
    private function generarConPlantilla(Certificado $certificado, string $plantilla): string
    {
        $campos = config('certificado_plantilla.campos');

        $pdf = new Fpdi('L', 'mm');

        // Fuentes personalizadas en resources/fonts/ (AddFont acepta $dir explícito)
        $fontDir = resource_path('fonts') . DIRECTORY_SEPARATOR;
        $pdf->AddFont('OptiDianna',    '', 'OptiDianna.json', $fontDir);
        $pdf->AddFont('CenturyGothic', '', 'GOTHIC.json',     $fontDir);

        $pdf->setSourceFile($plantilla);
        $pagina = $pdf->importPage(1);
        $tamano = $pdf->getTemplateSize($pagina);

        $pdf->AddPage($tamano['orientation'], [$tamano['width'], $tamano['height']]);
        $pdf->useTemplate($pagina, 0, 0, $tamano['width'], $tamano['height']);

        $pdf->SetTextColor(31, 41, 55);

        $this->escribirCampo($pdf, $campos['nombre_completo'], $certificado->capacitado->nombre_completo, $tamano['width']);
        $this->escribirCampo($pdf, $campos['documento'], $this->formatearDocumento($certificado->capacitado->documento), $tamano['width']);
        $this->escribirCampo($pdf, $campos['curso'], $certificado->curso->nombre, $tamano['width']);
        $this->escribirCampo($pdf, $campos['modalidad'], $certificado->modalidad ? ucfirst($certificado->modalidad) : 'No especificada', $tamano['width']);
        $this->escribirCampo($pdf, $campos['duracion'], (string) $certificado->intensidad_horaria, $tamano['width']);
        $this->escribirCampo($pdf, $campos['fecha_emision'], $certificado->fecha_emision->format('d/m/Y'), $tamano['width']);
        $this->escribirCampo($pdf, $campos['codigo_unico'], $certificado->codigo_unico, $tamano['width']);

        return $pdf->Output('S');
    }

    /** Formatea el documento con puntos cada 3 dígitos (ej: 1234567890 → 1.234.567.890). */
    private function formatearDocumento(string $documento): string
    {
        return preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $documento);
    }

    /** Escribe un campo individual respetando alineación, fuente y posición definidas en config. */
    private function escribirCampo(Fpdi $pdf, array $campo, string $texto, float $anchoPagina): void
    {
        $texto = mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');

        $pdf->SetFont($campo['fuente'] ?? 'Helvetica', $campo['estilo'], $campo['size']);

        if ($campo['align'] === 'C') {
            $ancho = $pdf->GetStringWidth($texto);
            $pdf->Text(($anchoPagina - $ancho) / 2, $campo['y'], $texto);

            return;
        }

        $pdf->Text($campo['x'], $campo['y'], $texto);
    }
}
