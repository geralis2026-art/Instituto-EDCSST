<?php

namespace Tests\Feature\Security;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre el checklist del Paso 8 de la feature multi-instructor: aislamiento
 * de datos por propietario, filtro de admin, y que el código único de
 * certificado siga siendo global (ver CONTEXTO_MULTI_INSTRUCTOR.md).
 */
class MultiInstructorTest extends TestCase
{
    use RefreshDatabase;

    // ── Instructor: solo ve lo suyo ───────────────────────────────────────────

    public function test_instructor_solo_ve_sus_propios_capacitados_en_el_listado(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $deEdna     = Capacitado::factory()->create(['user_id' => $edna->id]);
        $deMauricio = Capacitado::factory()->create(['user_id' => $mauricio->id]);

        $response = $this->actingAs($mauricio)->get('/admin/capacitados');

        $response->assertStatus(200);
        $response->assertSee($deMauricio->nombre_completo);
        $response->assertDontSee($deEdna->nombre_completo);
    }

    public function test_instructor_no_tiene_acceso_a_la_seccion_de_cursos(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        Curso::factory()->create(['user_id' => $edna->id]);
        $deMauricio = Curso::factory()->create(['user_id' => $mauricio->id]);

        // Solo Edna (admin) administra cursos: el instructor no puede ni listar los suyos.
        $this->actingAs($mauricio)->get('/admin/cursos')->assertStatus(403);
        $this->actingAs($mauricio)->get("/admin/cursos/{$deMauricio->id}")->assertStatus(403);
        $this->actingAs($mauricio)->get('/admin/cursos/create')->assertStatus(403);
    }

    public function test_instructor_solo_ve_sus_propios_certificados_en_el_listado(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $deEdna     = Certificado::factory()->create(['user_id' => $edna->id]);
        $deMauricio = Certificado::factory()->create(['user_id' => $mauricio->id]);

        $response = $this->actingAs($mauricio)->get('/admin/certificados');

        $response->assertStatus(200);
        $response->assertSee($deMauricio->codigo_unico);
        $response->assertDontSee($deEdna->codigo_unico);
    }

    // ── Instructor: no puede acceder por URL directa a lo de otro ────────────

    public function test_instructor_no_puede_ver_capacitado_ajeno_por_url_directa(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $deEdna   = Capacitado::factory()->create(['user_id' => $edna->id]);

        $this->actingAs($mauricio)->get("/admin/capacitados/{$deEdna->id}")->assertStatus(404);
    }

    public function test_instructor_no_puede_editar_capacitado_ajeno_por_url_directa(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $deEdna   = Capacitado::factory()->create(['user_id' => $edna->id]);

        $this->actingAs($mauricio)->get("/admin/capacitados/{$deEdna->id}/edit")->assertStatus(404);
        $this->actingAs($mauricio)->put("/admin/capacitados/{$deEdna->id}", [
            'nombre_completo' => 'Hackeado',
            'tipo_documento'  => 'CC',
            'documento'       => $deEdna->documento,
        ])->assertStatus(404);
    }

    public function test_instructor_no_puede_eliminar_capacitado_ajeno_por_url_directa(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $deEdna   = Capacitado::factory()->create(['user_id' => $edna->id]);

        $this->actingAs($mauricio)->delete("/admin/capacitados/{$deEdna->id}")->assertStatus(404);
        $this->assertDatabaseHas('capacitados', ['id' => $deEdna->id]);
    }

    public function test_instructor_no_puede_ver_ningun_curso_por_url_directa(): void
    {
        // Los cursos ya no son de un instructor: son un catálogo centralizado que
        // solo admin administra (ver EnsureUserCanViewCursos). El instructor no
        // puede acceder a la sección de cursos ni siquiera para uno propio/creado
        // por Edna, así sea solo lectura.
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $deEdna   = Curso::factory()->create(['user_id' => $edna->id]);

        $this->actingAs($mauricio)->get("/admin/cursos/{$deEdna->id}")->assertStatus(403);
        $this->actingAs($mauricio)->get("/admin/cursos/{$deEdna->id}/edit")->assertStatus(403);
    }

    public function test_instructor_no_puede_ver_certificado_ajeno_por_url_directa(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $deEdna   = Certificado::factory()->create(['user_id' => $edna->id]);

        $this->actingAs($mauricio)->get("/admin/certificados/{$deEdna->id}")->assertStatus(404);
        $this->actingAs($mauricio)->get("/admin/certificados/{$deEdna->id}/edit")->assertStatus(404);
    }

    // ── Instructor: CRUD normal sobre lo suyo ─────────────────────────────────

    public function test_instructor_puede_crear_su_propio_capacitado_y_queda_asignado_a_el(): void
    {
        $mauricio = User::factory()->instructor()->create();

        $response = $this->actingAs($mauricio)->post('/admin/capacitados', [
            'nombre_completo' => 'Juan Pérez',
            'tipo_documento'  => 'CC',
            'documento'       => '1234567890',
            'correo'          => 'juan@example.com',
            'telefono'        => '3001234567',
            'rh'              => 'O+',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('capacitados', [
            'documento' => '1234567890',
            'user_id'   => $mauricio->id,
        ]);
    }

    public function test_instructor_puede_ver_editar_y_eliminar_su_propio_capacitado(): void
    {
        $mauricio = User::factory()->instructor()->create();
        $suyo     = Capacitado::factory()->create(['user_id' => $mauricio->id]);

        $this->actingAs($mauricio)->get("/admin/capacitados/{$suyo->id}")->assertStatus(200);
        $this->actingAs($mauricio)->get("/admin/capacitados/{$suyo->id}/edit")->assertStatus(200);
        $this->actingAs($mauricio)->delete("/admin/capacitados/{$suyo->id}")->assertRedirect();
        $this->assertDatabaseMissing('capacitados', ['id' => $suyo->id]);
    }

    // ── Instructor: secciones exclusivas de admin siguen prohibidas ──────────

    public function test_instructor_no_puede_acceder_a_secciones_exclusivas_de_admin(): void
    {
        $mauricio = User::factory()->instructor()->create();

        $this->actingAs($mauricio)->get('/admin/usuarios')->assertStatus(403);
        $this->actingAs($mauricio)->get('/admin/mensajes')->assertStatus(403);
        $this->actingAs($mauricio)->get('/admin/categorias')->assertStatus(403);
        $this->actingAs($mauricio)->get('/admin/configuracion')->assertStatus(403);
    }

    // ── Admin: ve todo y el filtro funciona ───────────────────────────────────

    public function test_admin_ve_capacitados_de_todos_sin_filtro(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $deEdna     = Capacitado::factory()->create(['user_id' => $edna->id]);
        $deMauricio = Capacitado::factory()->create(['user_id' => $mauricio->id]);

        $response = $this->actingAs($edna)->get('/admin/capacitados');

        $response->assertStatus(200);
        $response->assertSee($deEdna->nombre_completo);
        $response->assertSee($deMauricio->nombre_completo);
    }

    public function test_admin_puede_filtrar_capacitados_por_instructor(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $deEdna     = Capacitado::factory()->create(['user_id' => $edna->id]);
        $deMauricio = Capacitado::factory()->create(['user_id' => $mauricio->id]);

        $response = $this->actingAs($edna)->get('/admin/capacitados?instructor=' . $mauricio->id);

        $response->assertStatus(200);
        $response->assertSee($deMauricio->nombre_completo);
        $response->assertDontSee($deEdna->nombre_completo);
    }

    public function test_admin_puede_ver_editar_y_eliminar_registros_de_cualquier_instructor(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();
        $deMauricio = Capacitado::factory()->create(['user_id' => $mauricio->id]);

        $this->actingAs($edna)->get("/admin/capacitados/{$deMauricio->id}")->assertStatus(200);
        $this->actingAs($edna)->get("/admin/capacitados/{$deMauricio->id}/edit")->assertStatus(200);
        $this->actingAs($edna)->delete("/admin/capacitados/{$deMauricio->id}")->assertRedirect();
        $this->assertDatabaseMissing('capacitados', ['id' => $deMauricio->id]);
    }

    // ── Instructor: no puede emitir certificados sobre datos ajenos ──────────

    public function test_instructor_no_puede_crear_certificado_sobre_capacitado_ajeno(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $capacitadoDeEdna = Capacitado::factory()->create(['user_id' => $edna->id]);
        $cursoDeMauricio  = Curso::factory()->create(['user_id' => $mauricio->id]);

        $response = $this->actingAs($mauricio)->post('/admin/certificados', [
            'capacitado_id'      => $capacitadoDeEdna->id,
            'curso_id'           => $cursoDeMauricio->id,
            'fecha_emision'      => '2025-01-15',
            'intensidad_horaria' => 40,
            'anios_vigencia'     => 1,
        ]);

        $response->assertSessionHasErrors('capacitado_id');
        $this->assertDatabaseMissing('certificados', ['capacitado_id' => $capacitadoDeEdna->id]);
    }

    public function test_instructor_puede_crear_certificado_sobre_curso_de_otro_gestor(): void
    {
        // Los cursos son un catálogo centralizado que solo admin administra: el
        // instructor no es dueño de ninguno, así que debe poder emitir certificados
        // usando cualquier curso activo (ver eliminación de PropietarioScope en Curso).
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $capacitadoDeMauricio = Capacitado::factory()->create(['user_id' => $mauricio->id]);
        $cursoDeEdna          = Curso::factory()->create(['user_id' => $edna->id]);

        $response = $this->actingAs($mauricio)->post('/admin/certificados', [
            'capacitado_id'      => $capacitadoDeMauricio->id,
            'curso_id'           => $cursoDeEdna->id,
            'fecha_emision'      => '2025-01-15',
            'intensidad_horaria' => 40,
            'anios_vigencia'     => 1,
        ]);

        $response->assertSessionDoesntHaveErrors('curso_id');
        $this->assertDatabaseHas('certificados', [
            'capacitado_id' => $capacitadoDeMauricio->id,
            'curso_id'      => $cursoDeEdna->id,
        ]);
    }

    // ── Código único de certificado: sigue siendo global ──────────────────────

    public function test_codigo_unico_de_certificado_no_colisiona_entre_instructores(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $anio = now()->year;

        Certificado::factory()->create([
            'user_id'      => $edna->id,
            'codigo_unico' => "EDCSST-{$anio}-00001",
        ]);

        // Sesión de instructor activa: el scope de propietario haría ver 0
        // certificados, pero el cálculo del código debe ignorar el scope.
        $this->actingAs($mauricio);
        $codigoParaInstructor = Certificado::generarCodigoUnico();

        $this->assertSame("EDCSST-{$anio}-00002", $codigoParaInstructor);
    }

    // ── Catálogo público: no cambia con el multi-instructor ──────────────────

    public function test_catalogo_publico_sigue_mostrando_cursos_de_todos_los_instructores(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $deEdna     = Curso::factory()->create(['user_id' => $edna->id, 'destacado' => true, 'activo' => true]);
        $deMauricio = Curso::factory()->create(['user_id' => $mauricio->id, 'destacado' => true, 'activo' => true]);

        $response = $this->get('/cursos');

        $response->assertStatus(200);
        $response->assertSee($deEdna->nombre);
        $response->assertSee($deMauricio->nombre);
    }
}
