<?php

/**
 * Coordenadas (en mm) de los campos variables que se escriben sobre la
 * plantilla de certificado (storage/app/public/plantillas/certificado.pdf,
 * configurada en configuracion_sitio.plantilla_certificado).
 *
 * Las posiciones se calcularon a partir de las coordenadas reales de las
 * etiquetas fijas de la plantilla (en puntos PDF, origen inferior izquierdo)
 * convertidas a mm con origen superior izquierdo (que es el que usa FPDF).
 * 'x' => null en campos centrados significa "centrar en el ancho de página".
 * 'fuente' es opcional, por defecto 'Helvetica'.
 *
 * Esta plantilla no tiene un campo de "fecha de vencimiento", por lo que
 * ese dato no se imprime (el certificado sigue venciendo internamente).
 */
return [
    'campos' => [
        'nombre_completo'   => ['x' => null,  'y' => 95.0,  'size' => 50, 'estilo' => '',  'fuente' => 'OptiDianna', 'align' => 'C'],
        'documento'         => ['x' => 129.0, 'y' => 105.7, 'size' => 14, 'estilo' => '',  'fuente' => 'CenturyGothic', 'align' => 'L'],
        'curso'             => ['x' => null,  'y' => 120.5, 'size' => 14, 'estilo' => 'B', 'fuente' => 'CenturyGothic', 'align' => 'C', 'color' => [89, 89, 89]],
        'modalidad'         => ['x' => 124.0, 'y' => 131.6, 'size' => 14, 'estilo' => 'B', 'fuente' => 'CenturyGothic', 'align' => 'L', 'color' => [89, 89, 89]],
        'duracion'          => ['x' => 168.0, 'y' => 130.5, 'size' => 9,  'estilo' => 'B', 'align' => 'L'],
        'fecha_emision'     => ['x' => 150.0, 'y' => 139.0, 'size' => 9,  'estilo' => '',  'align' => 'L'],
        'codigo_unico'      => ['x' => 150.0, 'y' => 143.2, 'size' => 9,  'estilo' => 'B', 'align' => 'L'],
    ],
];
