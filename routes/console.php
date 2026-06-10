<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Elimina diariamente los PDFs de certificados vencidos hace más de 1 año
// (el registro en BD se conserva, ver app/Console/Commands/LimpiarPdfsVencidos.php)
Schedule::command('certificados:limpiar-pdfs-vencidos')->daily();
