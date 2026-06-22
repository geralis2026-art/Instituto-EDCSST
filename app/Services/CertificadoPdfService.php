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

    /** DPI para renderizado de imágenes de texto. */
    private const IMAGEN_DPI = 300;

    /**
     * Renderiza el certificado y devuelve el binario del PDF.
     */
    public function generarPdf(Certificado $certificado): string
    {
        $certificado->load(['capacitado', 'curso.categoria']);

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

        // El nombre usa OptiDianna (fuente script) que Adobe Acrobat no puede renderizar
        // cuando está embebida por FPDF. Se renderiza como imagen PNG para compatibilidad total.
        $this->escribirNombreComoImagen($pdf, $campos['nombre_completo'], $nombre, $tamano['width']);

        $this->escribirCampo($pdf, $campos['documento'], $this->formatearDocumento($certificado->capacitado->documento), $tamano['width']);
        $this->escribirCampo($pdf, $campos['curso'], mb_strtoupper($certificado->curso->nombre, 'UTF-8'), $tamano['width']);
        $this->escribirCampo($pdf, $campos['modalidad'], ucfirst(strtolower($certificado->modalidad ?? 'No especificada')), $tamano['width']);
        $this->escribirCampo($pdf, $campos['duracion'], $certificado->intensidad_horaria . ' Horas', $tamano['width']);
        $this->escribirCampo($pdf, $campos['fecha_emision'], strtoupper($certificado->fecha_emision->format('Y/m/d')), $tamano['width']);
        $this->escribirCampo($pdf, $campos['codigo_unico'], mb_strtoupper($certificado->codigo_unico, 'UTF-8'), $tamano['width']);

        return $pdf->Output('S');
    }

    /**
     * Renderiza el nombre como imagen PNG usando GD + TTF para evitar problemas
     * de fuentes embebidas en Adobe Acrobat.
     */
    private function escribirNombreComoImagen(Fpdi $pdf, array $campo, string $nombre, float $anchoPagina): void
    {
        $ttf      = resource_path('fonts/OptiDianna.ttf');
        $dpi      = self::IMAGEN_DPI;
        $mmToPx   = $dpi / 25.4;
        $ptSize   = (float) $campo['size'];
        $minPt    = (float) ($campo['min_size'] ?? 8);
        $margenMm = (float) ($campo['margen']   ?? 20);
        $maxPx    = ($anchoPagina - $margenMm * 2) * $mmToPx;

        // Tamaño de fuente GD en píxeles (1pt = DPI/72 px)
        $giFontPx = $ptSize * $dpi / 72.0;

        // Reduce el tamaño hasta que el texto quepa en el ancho disponible
        $bbox = imagettfbbox($giFontPx, 0, $ttf, $nombre);
        $textW = abs($bbox[2] - $bbox[0]);

        while ($textW > $maxPx && $ptSize > $minPt) {
            $ptSize  -= 0.5;
            $giFontPx = $ptSize * $dpi / 72.0;
            $bbox     = imagettfbbox($giFontPx, 0, $ttf, $nombre);
            $textW    = abs($bbox[2] - $bbox[0]);
        }

        // Dimensiones de la imagen
        $ascender   = -$bbox[7];            // píxeles desde baseline hasta arriba del texto
        $descender  = max(0, $bbox[1]);     // píxeles desde baseline hacia abajo
        $textH      = $ascender + $descender;
        $paddingPx  = (int) round($mmToPx * 2); // 2 mm de margen vertical
        $imgW       = (int) round($anchoPagina * $mmToPx);
        $imgH       = $textH + $paddingPx * 2;

        // Crear imagen con fondo transparente
        $img = imagecreatetruecolor($imgW, $imgH);
        imagesavealpha($img, true);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));

        [$r, $g, $b] = self::COLOR_TEXTO_BASE;
        $color = imagecolorallocate($img, $r, $g, $b);

        // Centrar horizontalmente; baseline = paddingPx + ascender
        $textX = (int) round(($imgW - $textW) / 2 - $bbox[0]);
        $textY = $paddingPx + $ascender;

        imagettftext($img, $giFontPx, 0, $textX, $textY, $color, $ttf, $nombre);

        $tmpFile = tempnam(sys_get_temp_dir(), 'cert_img_') . '.png';
        imagepng($img, $tmpFile);
        imagedestroy($img);

        // Convertir dimensiones a mm y posicionar en el PDF
        $imgHMm  = $imgH / $mmToPx;
        // campo['y'] es la baseline en mm; el top de la imagen queda arriba de eso
        $yTopMm  = $campo['y'] - ($ascender + $paddingPx) / $mmToPx;

        $pdf->Image($tmpFile, 0, $yTopMm, $anchoPagina, $imgHMm, 'PNG');

        @unlink($tmpFile);
    }

    /** Formatea el documento con puntos cada 3 dígitos. */
    private function formatearDocumento(string $documento): string
    {
        return preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $documento);
    }

    /** Escribe un campo individual respetando alineación, fuente y posición. */
    private function escribirCampo(Fpdi $pdf, array $campo, string $texto, float $anchoPagina): void
    {
        $texto = mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');

        $fuente   = $campo['fuente'] ?? 'Helvetica';
        $estilo   = $campo['estilo'];
        $size     = (float) $campo['size'];
        $minSize  = (float) ($campo['min_size'] ?? 8);
        $margen   = (float) ($campo['margen'] ?? 20);
        $maxAncho = $anchoPagina - ($margen * 2);

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
