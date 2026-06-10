<?php

namespace App\Console\Commands;

use App\Models\Certificado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Elimina los archivos PDF de certificados cuya fecha de vencimiento
 * pasó hace más de un año. El registro en base de datos se conserva
 * (sigue siendo verificable y cuenta para horas_capacitadas).
 *
 * Uso: php artisan certificados:limpiar-pdfs-vencidos
 */
class LimpiarPdfsVencidos extends Command
{
    protected $signature = 'certificados:limpiar-pdfs-vencidos';

    protected $description = 'Elimina los PDFs de certificados vencidos hace más de un año, conservando el registro';

    public function handle(): int
    {
        $limite = now()->subYear()->toDateString();

        $certificados = Certificado::query()
            ->whereNotNull('archivo_pdf')
            ->where('fecha_vencimiento', '<', $limite)
            ->get();

        if ($certificados->isEmpty()) {
            $this->info('No hay PDFs de certificados vencidos para eliminar.');

            return self::SUCCESS;
        }

        $eliminados = 0;

        foreach ($certificados as $certificado) {
            Storage::disk('certificados')->delete($certificado->archivo_pdf);
            $certificado->update(['archivo_pdf' => null]);
            $eliminados++;
        }

        $this->info("✓ {$eliminados} PDF(s) de certificados vencidos eliminado(s) (registros conservados).");

        return self::SUCCESS;
    }
}
