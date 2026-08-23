<?php

namespace Tests\Feature\Admin;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Certificado::guardarConCodigoUnico() debe reintentar cuando dos procesos
 * concurrentes calculan el mismo "siguiente número" antes de que el primero
 * confirme el suyo — escenario más probable ahora que varios instructores
 * pueden emitir certificados al mismo tiempo (ver multi-instructor).
 */
class CertificadoCodigoUnicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guardar_con_codigo_unico_reintenta_ante_colision_real(): void
    {
        $anio = now()->year;

        // Certificado existente que ocupa el código "00001".
        Certificado::factory()->create(['codigo_unico' => "EDCSST-{$anio}-00001"]);

        $capacitado = Capacitado::factory()->create();
        $curso      = Curso::factory()->create();

        // Subclase de prueba: fuerza que el primer intento devuelva un código
        // que YA existe (colisión real detectada por el UNIQUE de la BD), y
        // que el segundo intento use el cálculo real (generarCodigoUnico()).
        $certificado = new class extends Certificado {
            public static int $llamadas = 0;

            public static function generarCodigoUnico(): string
            {
                static::$llamadas++;

                return static::$llamadas === 1
                    ? 'EDCSST-' . now()->year . '-00001'
                    : parent::generarCodigoUnico();
            }
        };

        $certificado->fill([
            'capacitado_id'      => $capacitado->id,
            'curso_id'           => $curso->id,
            'codigo_unico'       => (string) Str::uuid(),
            'fecha_emision'      => now()->toDateString(),
            'fecha_vencimiento'  => now()->addYear()->toDateString(),
            'intensidad_horaria' => 40,
            'activo'             => true,
        ]);

        $certificado->guardarConCodigoUnico();

        $this->assertSame(2, $certificado::$llamadas, 'Debió reintentar exactamente una vez.');
        $this->assertSame("EDCSST-{$anio}-00002", $certificado->codigo_unico);
        $this->assertDatabaseHas('certificados', [
            'id'           => $certificado->id,
            'codigo_unico' => "EDCSST-{$anio}-00002",
        ]);
    }

    public function test_guardar_con_codigo_unico_relanza_si_agota_los_intentos(): void
    {
        $anio = now()->year;
        Certificado::factory()->create(['codigo_unico' => "EDCSST-{$anio}-00001"]);

        $capacitado = Capacitado::factory()->create();
        $curso      = Curso::factory()->create();

        // Siempre devuelve el mismo código ya ocupado: debe agotar los
        // reintentos y relanzar la excepción, no fallar silenciosamente.
        $certificado = new class extends Certificado {
            public static function generarCodigoUnico(): string
            {
                return 'EDCSST-' . now()->year . '-00001';
            }
        };

        $certificado->fill([
            'capacitado_id'      => $capacitado->id,
            'curso_id'           => $curso->id,
            'codigo_unico'       => (string) Str::uuid(),
            'fecha_emision'      => now()->toDateString(),
            'fecha_vencimiento'  => now()->addYear()->toDateString(),
            'intensidad_horaria' => 40,
            'activo'             => true,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $certificado->guardarConCodigoUnico(3);
    }

    /**
     * La numeración es lineal a propósito: al eliminar un certificado su
     * número NO se reutiliza, para no mezclar el orden de emisión con
     * códigos "fuera de secuencia".
     */
    public function test_eliminar_un_certificado_no_reutiliza_su_codigo(): void
    {
        $anio = now()->year;

        $c1 = Certificado::factory()->create(['codigo_unico' => "EDCSST-{$anio}-00001"]);
        Certificado::factory()->create(['codigo_unico' => "EDCSST-{$anio}-00002"]);
        Certificado::factory()->create(['codigo_unico' => "EDCSST-{$anio}-00003"]);

        $c1->delete();

        // Aunque el 00001 quedó libre, el siguiente sigue siendo 00004.
        $this->assertSame("EDCSST-{$anio}-00004", Certificado::generarCodigoUnico());
    }
}
