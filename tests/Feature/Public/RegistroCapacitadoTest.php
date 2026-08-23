<?php

namespace Tests\Feature\Public;

use App\Models\Capacitado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RegistroCapacitadoTest extends TestCase
{
    use RefreshDatabase;

    private function token(?int $empleadoId = null): string
    {
        // En producción el token siempre trae un id de empleado real (la
        // ruta que lo genera exige sesión activa); un valor null en caché
        // haría que Cache::has() lo tratara como "no existe".
        $empleadoId ??= User::factory()->admin()->create()->id;

        $token = 'test-token-registro-'.uniqid();
        Cache::put("reg:{$token}", $empleadoId, now()->addMinutes(20));

        return $token;
    }

    public function test_formulario_muestra_error_si_el_token_no_es_valido(): void
    {
        $this->get('/registro/token-invalido')->assertSee('expirado');
    }

    public function test_registro_crea_capacitado_y_solicitudes(): void
    {
        $curso = Curso::factory()->create(['activo' => true]);
        $token = $this->token();

        $response = $this->post("/registro/{$token}", [
            'nombre_completo' => 'Ana Pérez',
            'tipo_documento'  => 'CC',
            'documento'       => '900123456',
            'cursos'          => [$curso->id],
            'modalidades'     => [$curso->id => 'virtual'],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('capacitados', ['documento' => '900123456']);
        $this->assertDatabaseHas('solicitudes_certificado', [
            'curso_id' => $curso->id,
            'modalidad' => 'virtual',
        ]);
    }

    /**
     * Reproduce el bug real: un instructor con sesión activa en el mismo
     * navegador (guard "web") abre el link público. Capacitado tiene
     * PropietarioScope, así que updateOrCreate() sin bypass no encontraría
     * un documento ya existente de otro dueño e intentaría insertarlo de
     * nuevo, violando el UNIQUE de la BD (error 500 reportado en producción).
     */
    public function test_registro_no_falla_si_el_documento_ya_existe_de_otro_dueno_y_hay_instructor_logueado(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $curso    = Curso::factory()->create(['activo' => true]);

        $existente = Capacitado::factory()->create([
            'documento' => '900123456',
            'user_id'   => $edna->id,
        ]);

        $token = $this->token();

        $response = $this->actingAs($mauricio)->post("/registro/{$token}", [
            'nombre_completo' => 'Ana Pérez Actualizada',
            'tipo_documento'  => 'CC',
            'documento'       => '900123456',
            'cursos'          => [$curso->id],
            'modalidades'     => [$curso->id => 'presencial'],
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('capacitados', 1);
        $this->assertDatabaseHas('capacitados', [
            'id'              => $existente->id,
            'nombre_completo' => 'Ana Pérez Actualizada',
        ]);
    }

    /**
     * Reproduce el bug reportado: un capacitado nuevo registrado por el link
     * quedaba con user_id nulo, invisible para el instructor que generó ese
     * link (PropietarioScope solo le muestra lo suyo). El token ahora guarda
     * el id del empleado que lo generó para asignarlo como dueño.
     */
    public function test_capacitado_nuevo_queda_asignado_al_empleado_que_genero_el_link(): void
    {
        $mauricio = User::factory()->instructor()->create();
        $curso    = Curso::factory()->create(['activo' => true]);
        $token    = $this->token($mauricio->id);

        $this->post("/registro/{$token}", [
            'nombre_completo' => 'Nuevo Capacitado',
            'tipo_documento'  => 'CC',
            'documento'       => '900999888',
            'cursos'          => [$curso->id],
            'modalidades'     => [$curso->id => 'virtual'],
        ])->assertOk();

        $this->assertDatabaseHas('capacitados', [
            'documento' => '900999888',
            'user_id'   => $mauricio->id,
        ]);

        // Ahora sí debe aparecer en el listado de Mauricio.
        $this->actingAs($mauricio)
            ->get('/admin/capacitados')
            ->assertSee('Nuevo Capacitado');
    }

    public function test_registro_valida_al_menos_un_curso(): void
    {
        $token = $this->token();

        $this->post("/registro/{$token}", [
            'nombre_completo' => 'Ana Pérez',
            'documento'       => '900123456',
        ])->assertSessionHasErrors('cursos');
    }
}
