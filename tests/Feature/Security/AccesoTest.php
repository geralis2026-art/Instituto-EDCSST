<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica que la capa de acceso (auth, activo, admin) funcione
 * correctamente para todas las combinaciones de usuario/ruta.
 */
class AccesoTest extends TestCase
{
    use RefreshDatabase;

    // ── Invitado (sin sesión) ─────────────────────────────────────────────────

    public function test_invitado_redirigido_al_login_desde_admin(): void
    {
        $this->get('/admin/capacitados')->assertRedirect(route('login'));
        $this->get('/admin/certificados')->assertRedirect(route('login'));
        $this->get('/admin/cursos')->assertRedirect(route('login'));
        $this->get('/admin/mensajes')->assertRedirect(route('login'));
        $this->get('/admin/usuarios')->assertRedirect(route('login'));
        $this->get('/admin/configuracion')->assertRedirect(route('login'));
    }

    public function test_ruta_registro_publico_devuelve_403(): void
    {
        $this->get('/register')->assertStatus(403);
        $this->post('/register')->assertStatus(403);
    }

    // ── Usuario inactivo ──────────────────────────────────────────────────────

    public function test_usuario_inactivo_es_deslogueado_y_redirigido(): void
    {
        $usuario = User::factory()->inactivo()->create();

        $this->actingAs($usuario)
            ->get('/admin/capacitados')
            ->assertRedirect(route('login'));
    }

    // ── Capacitador: rutas permitidas ─────────────────────────────────────────

    public function test_capacitador_puede_listar_capacitados(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/capacitados')->assertStatus(200);
    }

    public function test_capacitador_puede_listar_certificados(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/certificados')->assertStatus(200);
    }

    public function test_capacitador_puede_acceder_al_formulario_de_nuevo_certificado(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/certificados/create')->assertStatus(200);
    }

    // ── Capacitador: rutas prohibidas ─────────────────────────────────────────

    public function test_capacitador_no_puede_crear_capacitado(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/capacitados/create')->assertStatus(403);
    }

    public function test_capacitador_no_puede_acceder_a_cursos(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/cursos')->assertStatus(403);
    }

    public function test_capacitador_no_puede_acceder_a_categorias(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/categorias')->assertStatus(403);
    }

    public function test_capacitador_no_puede_acceder_a_mensajes(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/mensajes')->assertStatus(403);
    }

    public function test_capacitador_no_puede_acceder_a_usuarios(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/usuarios')->assertStatus(403);
    }

    public function test_capacitador_no_puede_acceder_a_configuracion(): void
    {
        $cap = User::factory()->capacitador()->create();
        $this->actingAs($cap)->get('/admin/configuracion')->assertStatus(403);
    }

    // ── Admin: acceso total ────────────────────────────────────────────────────

    public function test_admin_puede_acceder_a_todas_las_secciones(): void
    {
        $admin = User::factory()->admin()->create();

        $rutas = [
            '/admin/capacitados',
            '/admin/certificados',
            '/admin/certificados/create',
            '/admin/capacitados/create',
            '/admin/cursos',
            '/admin/cursos/create',
            '/admin/categorias',
            '/admin/categorias/create',
            '/admin/mensajes',
            '/admin/usuarios',
            '/admin/configuracion',
        ];

        foreach ($rutas as $ruta) {
            $this->actingAs($admin)->get($ruta)->assertStatus(200, "Falló en: {$ruta}");
        }
    }
}
