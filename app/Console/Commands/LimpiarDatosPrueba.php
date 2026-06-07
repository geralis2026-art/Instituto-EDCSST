<?php

namespace App\Console\Commands;

use App\Models\Capacitado;
use App\Models\Certificado;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
                            {--force : Omitir la confirmación interactiva (para scripts)}
                            {--solo-certificados : Solo borrar certificados y PDFs, conservar capacitados y cursos}';

    protected $description = 'Elimina certificados (y opcionalmente capacitados) de prueba, reiniciando los contadores';

    public function handle(): int
    {
        if ($this->option('solo-certificados')) {
            return $this->limpiarSoloCertificados();
        }

        return $this->limpiarTodo();
    }

    private function limpiarSoloCertificados(): int
    {
        $totalCertificados = Certificado::count();
        $pdfs              = Storage::disk('local')->files('certificados');
        $totalPdfs         = count($pdfs);

        if ($totalCertificados === 0) {
            $this->info('No hay certificados que limpiar.');
            return self::SUCCESS;
        }

        $this->warn('Se eliminarán únicamente los certificados (capacitados y cursos se conservan):');
        $this->line("  • Certificados : {$totalCertificados}");
        $this->line("  • PDFs en storage: {$totalPdfs}");

        if (! $this->option('force') && ! $this->confirm('¿Confirmas? Esta acción no se puede deshacer.')) {
            $this->line('Operación cancelada.');
            return self::SUCCESS;
        }

        $pdfsEliminados = 0;
        foreach ($pdfs as $archivo) {
            if (Storage::disk('local')->delete($archivo)) {
                $pdfsEliminados++;
            }
        }

        Certificado::query()->delete();
        DB::statement('ALTER TABLE certificados AUTO_INCREMENT = 1');

        // Recalcular horas de todos los capacitados (quedarán en 0)
        Capacitado::query()->update(['horas_capacitadas' => 0]);

        $this->info("✓ {$totalCertificados} certificado(s) eliminado(s).");
        $this->info("✓ {$pdfsEliminados} PDF(s) eliminado(s) del storage.");
        $this->info('✓ Contador de certificados reiniciado a 1.');
        $this->newLine();
        $this->info('Certificados limpiados. Los capacitados y cursos se conservaron.');

        return self::SUCCESS;
    }

    private function limpiarTodo(): int
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

        $pdfsEliminados = 0;
        foreach ($pdfs as $archivo) {
            if (Storage::disk('local')->delete($archivo)) {
                $pdfsEliminados++;
            }
        }

        Certificado::query()->delete();
        Capacitado::query()->delete();

        DB::statement('ALTER TABLE certificados AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE capacitados AUTO_INCREMENT = 1');

        $this->info("✓ {$totalCertificados} certificado(s) eliminado(s).");
        $this->info("✓ {$totalCapacitados} capacitado(s) eliminado(s).");
        $this->info("✓ {$pdfsEliminados} PDF(s) eliminado(s) del storage.");
        $this->newLine();
        $this->info('El sistema está limpio y listo para datos reales.');

        return self::SUCCESS;
    }
}
