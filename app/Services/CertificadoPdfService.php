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
    /** Color de texto predeterminado para el overlay (gray-900 Tailwind ≈ rgb 31,41,55). */
    private const COLOR_TEXTO_BASE = [31, 41, 55];
    /**
     * Renderiza el certificado y devuelve el binario del PDF.
     * Usa Storage::disk('public') para verificar y resolver la plantilla:
     * Flysystem confina las operaciones al root del disco, previniendo
     * path traversal sin validación manual adicional.
     */
    public function generarPdf(Certificado $certificado): string
    {
        $certificado->loadMissing(['capacitado', 'curso.categoria']);

        $rutaRelativa = ConfiguracionSitio::obtener()->plantilla_certificado;

        if ($rutaRelativa && Storage::disk('public')->exists($rutaRelativa)) {
            return $this->generarConPlantilla($certificado, Storage::disk('public')->path($rutaRelativa));
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
        $pdf->AddFont('OptiDianna',    '',  'OptiDianna.json', $fontDir);
        $pdf->AddFont('CenturyGothic', '',  'GOTHIC.json',    $fontDir);
        $pdf->AddFont('CenturyGothic', 'B', 'GOTHICB.json',   $fontDir);

        $pdf->setSourceFile($plantilla);
        $pagina = $pdf->importPage(1);
        $tamano = $pdf->getTemplateSize($pagina);

        $pdf->AddPage($tamano['orientation'], [$tamano['width'], $tamano['height']]);
        $pdf->useTemplate($pagina, 0, 0, $tamano['width'], $tamano['height']);

        $pdf->SetTextColor(...self::COLOR_TEXTO_BASE);

        $nombre = mb_convert_case($certificado->capacitado->nombre_completo, MB_CASE_TITLE, 'UTF-8');
        $this->escribirCampo($pdf, $campos['nombre_completo'], $nombre, $tamano['width']);
        $this->escribirCampo($pdf, $campos['documento'], $this->formatearDocumento($certificado->capacitado->documento), $tamano['width']);
        $this->escribirCampo($pdf, $campos['curso'], mb_strtoupper($certificado->curso->nombre, 'UTF-8'), $tamano['width']);
        $this->escribirCampo($pdf, $campos['modalidad'], ucfirst(strtolower($certificado->modalidad ?? 'No especificada')), $tamano['width']);
        $this->escribirCampo($pdf, $campos['duracion'], $certificado->intensidad_horaria . ' Horas', $tamano['width']);
        $this->escribirCampo($pdf, $campos['fecha_emision'], strtoupper($certificado->fecha_emision->format('Y/m/d')), $tamano['width']);
        $this->escribirCampo($pdf, $campos['codigo_unico'], mb_strtoupper($certificado->codigo_unico, 'UTF-8'), $tamano['width']);

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

        $fuente   = $campo['fuente'] ?? 'Helvetica';
        $estilo   = $campo['estilo'];
        $size     = (float) $campo['size'];
        $minSize  = (float) ($campo['min_size'] ?? 8);
        $margen   = (float) ($campo['margen'] ?? 20);
        $maxAncho = $anchoPagina - ($margen * 2);

        // Reduce el tamaño de fuente hasta que el texto quepa en el ancho disponible.
        $pdf->SetFont($fuente, $estilo, $size);
        while ($size > $minSize && $pdf->GetStringWidth($texto) > $maxAncho) {
            $size -= 0.5;
            $pdf->SetFont($fuente, $estilo, $size);
        }

        if (isset($campo['color'])) {
            $pdf->SetTextColor(...$campo['color']);
        }

        if ($campo['align'] === 'C') {
            $ancho = $pdf->GetStringWidth($texto);
            $pdf->Text(($anchoPagina - $ancho) / 2, $campo['y'], $texto);
        } else {
            $pdf->Text($campo['x'], $campo['y'], $texto);
        }

        if (isset($campo['color'])) {
            $pdf->SetTextColor(...self::COLOR_TEXTO_BASE);
        }
    }
}
