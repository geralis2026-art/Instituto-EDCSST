<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Regresión: con `CERTIFICADOS_DISK_ROOT=` (presente pero vacío) en el .env, el disco quedaba sin ruta
 * ("Unable to create a directory at .") y fallaba al guardar o regenerar el PDF de un certificado.
 */
class ConfiguracionDiscosTest extends TestCase
{
    public function test_variables_de_disco_vacias_usan_la_ruta_por_defecto(): void
    {
        $claves = ['CERTIFICADOS_DISK_ROOT', 'UPLOADS_DISK_ROOT'];
        $previo = [];

        foreach ($claves as $clave) {
            $previo[$clave] = [$_ENV[$clave] ?? null, $_SERVER[$clave] ?? null];
            $_ENV[$clave] = $_SERVER[$clave] = '';
        }

        try {
            $discos = (require config_path('filesystems.php'))['disks'];

            $this->assertSame(storage_path('app/private'), $discos['certificados']['root']);
            $this->assertSame(storage_path('app/uploads'), $discos['uploads']['root']);
        } finally {
            foreach ($previo as $clave => [$env, $server]) {
                $env === null ? $this->unsetGlobal($_ENV, $clave) : $_ENV[$clave] = $env;
                $server === null ? $this->unsetGlobal($_SERVER, $clave) : $_SERVER[$clave] = $server;
            }
        }
    }

    public function test_una_ruta_configurada_en_el_env_se_respeta(): void
    {
        $_ENV['CERTIFICADOS_DISK_ROOT'] = $_SERVER['CERTIFICADOS_DISK_ROOT'] = '/ruta/propia/certificados';

        try {
            $discos = (require config_path('filesystems.php'))['disks'];

            $this->assertSame('/ruta/propia/certificados', $discos['certificados']['root']);
        } finally {
            unset($_ENV['CERTIFICADOS_DISK_ROOT'], $_SERVER['CERTIFICADOS_DISK_ROOT']);
        }
    }

    private function unsetGlobal(array &$arreglo, string $clave): void
    {
        unset($arreglo[$clave]);
    }
}
