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
 */
return [
    'campos' => [
        'nombre_completo'   => ['x' => null,  'y' => 91.6,  'size' => 20, 'estilo' => 'B', 'align' => 'C'],
        'documento'         => ['x' => 136.2, 'y' => 103.0, 'size' => 9,  'estilo' => '',  'align' => 'L'],
        'curso'             => ['x' => null,  'y' => 119.6, 'size' => 12, 'estilo' => 'B', 'align' => 'C'],
        'modalidad'         => ['x' => 136.2, 'y' => 129.5, 'size' => 9,  'estilo' => 'B', 'align' => 'L'],
        'duracion'          => ['x' => 174.7, 'y' => 129.5, 'size' => 9,  'estilo' => 'B', 'align' => 'L'],
        'fecha_emision'     => ['x' => 118.9, 'y' => 138.6, 'size' => 9,  'estilo' => '',  'align' => 'L'],
        'fecha_vencimiento' => ['x' => 182.5, 'y' => 138.6, 'size' => 9,  'estilo' => '',  'align' => 'L'],
        'codigo_unico'      => ['x' => 157.4, 'y' => 144.1, 'size' => 9,  'estilo' => 'B', 'align' => 'L'],
    ],
];
