<?php

/**
 * Datos institucionales fijos usados al generar el PDF del certificado
 * con la plantilla del instituto (ver resources/views/pdf/certificado.blade.php).
 */
return [
    'firmante_nombre' => 'Mauricio Monroy Patiño',
    'firmante_cargo' => 'Representante Legal',
    'firmante_detalle' => [
        'Médico RM: 1116548453',
        'Especialista en SST 24754125',
    ],

    'ciudad_expedicion' => 'Villavicencio - Meta',

    'texto_resolucion' => 'En este curso cumple con los establecidos para la resolución 3280 de 2018',

    'texto_legal' => 'La presente certificación se sustenta en el Decreto 1075 de 2015, artículo 2.6.6.8, '
        . 'referente a la Educación Informal, y en el Decreto 376 de 2022 del Ministerio de Educación Nacional, '
        . 'que establece el Registro de Talento Humano en Educación Continua y Permanente de Salud.',
];
