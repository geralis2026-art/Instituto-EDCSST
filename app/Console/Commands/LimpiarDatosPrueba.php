<?php

namespace App\Console\Commands;

use App\Models\Capacitado;
use App\Models\Certificado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Elimina todos los certificados y capacitados de prueba antes de entrar en producción.
 * También borra los PDFs del storage para dejar el sistema completamente limpio.
 *
 * Uso: php artisan datos:limpiar
 */
class LimpiarDatosPrueba extends Command
{
    protected $signature = 'datos:limpiar
                            {--force : Omitir la confirmación interactiva (para scripts)}';

    protected $description = 'Elimina todos los capacitados, certificados y PDFs de prueba';

    public function handle(): int
    {
        $totalCertificados = Certificado::count();
        $totalCapacitados  = Capacitado::count();
        $pdfs              = Storage::disk('local')->files('certificados');
        $totalPdfs         = count($pdfs);

        if ($totalCapacitados === 0 && $totalCertificados === 0) {
            $this->info('No hay datos de prueba que limpiar. La base de datos ya está vacía.');
            return self::SUCCESS;
        }

        $this->warn('Se eliminarán los siguientes registros de forma permanente:');
        $this->line("  • Certificados : {$totalCertificados}");
        $this->line("  • Capacitados  : {$totalCapacitados}");
        $this->line("  • PDFs en storage: {$totalPdfs}");

        if (! $this->option('force') && ! $this->confirm('¿Confirmas que deseas borrar todos estos datos? Esta acción no se puede deshacer.')) {
            $this->line('Operación cancelada.');
            return self::SUCCESS;
        }

        // 1. Borrar PDFs del storage antes de eliminar los registros
        $pdfsEliminados = 0;
        foreach ($pdfs as $archivo) {
            if (Storage::disk('local')->delete($archivo)) {
                $pdfsEliminados++;
            }
        }

        // 2. Eliminar certificados (la FK con capacitados tiene cascadeOnDelete,
        //    pero borramos explícitamente para disparar los eventos del modelo
        //    y recalcular horas_capacitadas correctamente)
        Certificado::query()->delete();

        // 3. Eliminar capacitados
        Capacitado::query()->delete();

        $this->info("✓ {$totalCertificados} certificado(s) eliminado(s).");
        $this->info("✓ {$totalCapacitados} capacitado(s) eliminado(s).");
        $this->info("✓ {$pdfsEliminados} PDF(s) eliminado(s) del storage.");
        $this->newLine();
        $this->info('El sistema está limpio y listo para datos reales.');

        return self::SUCCESS;
    }
}
