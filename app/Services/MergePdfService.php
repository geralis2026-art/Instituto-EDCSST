<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

/**
 * Fusiona varios archivos PDF existentes en uno solo, página por página,
 * sin almacenar el resultado (se genera al vuelo para la descarga).
 */
class MergePdfService
{
    /**
     * @param  array<int, string>  $rutasAbsolutas  Rutas absolutas a archivos PDF existentes
     * @return string  Contenido binario del PDF resultante
     */
    public function fusionar(array $rutasAbsolutas): string
    {
        $pdf = new Fpdi();

        foreach ($rutasAbsolutas as $ruta) {
            $totalPaginas = $pdf->setSourceFile($ruta);

            for ($pagina = 1; $pagina <= $totalPaginas; $pagina++) {
                $plantillaId = $pdf->importPage($pagina);
                $tamano = $pdf->getTemplateSize($plantillaId);

                $pdf->AddPage($tamano['orientation'], [$tamano['width'], $tamano['height']]);
                $pdf->useTemplate($plantillaId);
            }
        }

        return $pdf->Output('S');
    }
}
