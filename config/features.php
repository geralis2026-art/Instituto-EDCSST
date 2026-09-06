<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Funcionalidades con activación independiente
    |--------------------------------------------------------------------------
    |
    | Módulos de la Fase 3 aún no pagados por el cliente: quedan ocultos y
    | bloqueados (404) hasta que se confirme el pago, sin necesidad de tocar
    | código ni volver a desplegar — solo cambiar el valor en el .env de
    | producción a "true".
    |
    */

    'aula_virtual' => (bool) env('FEATURE_AULA_VIRTUAL', false),

    'comunicaciones' => (bool) env('FEATURE_COMUNICACIONES', false),

];
