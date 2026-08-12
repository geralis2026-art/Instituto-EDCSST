<?php

namespace App\Console\Commands;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('asignar:historico')]
#[Description('Asigna el user_id del histórico de cursos, capacitados y certificados al admin original (multi-instructor)')]
class AsignarHistoricoAEdna extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admin = User::where('rol', User::ROL_ADMIN)->orderBy('id')->first();

        if (! $admin) {
            $this->error('No existe ningún usuario con rol admin. Abortando.');

            return self::FAILURE;
        }

        $cursos       = Curso::whereNull('user_id')->count();
        $capacitados  = Capacitado::whereNull('user_id')->count();
        $certificados = Certificado::whereNull('user_id')->count();

        $this->info("Usuario destino: {$admin->name} ({$admin->email}, id={$admin->id})");
        $this->table(
            ['Tabla', 'Registros sin user_id'],
            [
                ['cursos', $cursos],
                ['capacitados', $capacitados],
                ['certificados', $certificados],
            ]
        );

        if ($cursos + $capacitados + $certificados === 0) {
            $this->info('No hay registros pendientes por asignar.');

            return self::SUCCESS;
        }

        if (! $this->confirm('¿Confirmas asignar estos registros al usuario indicado?')) {
            $this->warn('Operación cancelada.');

            return self::SUCCESS;
        }

        Curso::whereNull('user_id')->update(['user_id' => $admin->id]);
        Capacitado::whereNull('user_id')->update(['user_id' => $admin->id]);
        Certificado::whereNull('user_id')->update(['user_id' => $admin->id]);

        $this->info('Histórico asignado correctamente.');

        return self::SUCCESS;
    }
}
