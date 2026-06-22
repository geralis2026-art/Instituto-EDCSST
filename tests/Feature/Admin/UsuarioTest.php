<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $capacitador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin       = User::factory()->admin()->create();
        $this->capacitador = User::factory()->capacitador()->create();
    }

    // ── Acceso por rol ────────────────────────────────────────────────────────

    public function test_admin_puede_listar_usuarios(): void
    {
        $this->actingAs($this->admin)->get('/admin/usuarios')->assertStatus(200);
    }

    public function test_capacitador_no_puede_listar_usuarios(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/usuarios')->assertStatus(403);
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_index_muestra_usuarios_del_sistema(): void
    {
        $this->actingAs($this->admin)->get('/admin/usuarios')
            ->assertSee($this->admin->name);
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->admin)->get('/admin/usuarios/create')->assertStatus(200);
    }

    public function test_admin_puede_crear_usuario_capacitador(): void
    {
        $this->actingAs($this->admin)->post('/admin/usuarios', [
            'name'                  => 'Nuevo Capacitador',
            'email'                 => 'capacitador@edcsst.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'rol'                   => 'capacitador',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email'  => 'capacitador@edcsst.com',
            'rol'    => 'capacitador',
            'activo' => false,
        ]);
    }

    public function test_usuario_creado_queda_inactivo_por_defecto(): void
    {
        $this->actingAs($this->admin)->post('/admin/usuarios', [
            'name'                  => 'Test Inactivo',
            'email'                 => 'inactivo@edcsst.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'rol'                   => 'capacitador',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'inactivo@edcsst.com', 'activo' => false]);
    }

    public function test_store_valida_campos_requeridos(): void
    {
        $this->actingAs($this->admin)->post('/admin/usuarios', [])
            ->assertSessionHasErrors(['name', 'email', 'password', 'rol']);
    }

    public function test_store_valida_email_unico(): void
    {
        $this->actingAs($this->admin)->post('/admin/usuarios', [
            'name'                  => 'Duplicado',
            'email'                 => $this->admin->email,
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'rol'                   => 'admin',
        ])->assertSessionHasErrors('email');
    }

    public function test_store_valida_contrasenas_coincidan(): void
    {
        $this->actingAs($this->admin)->post('/admin/usuarios', [
            'name'                  => 'Test',
            'email'                 => 'nuevo@edcsst.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'OtraContrasena456!',
            'rol'                   => 'capacitador',
        ])->assertSessionHasErrors('password');
    }

    public function test_store_valida_rol_valido(): void
    {
        $this->actingAs($this->admin)->post('/admin/usuarios', [
            'name'                  => 'Test',
            'email'                 => 'test@edcsst.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'rol'                   => 'superusuario',
        ])->assertSessionHasErrors('rol');
    }

    // ── Toggle activo ─────────────────────────────────────────────────────────

    public function test_admin_puede_activar_usuario_inactivo(): void
    {
        $usuario = User::factory()->inactivo()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/usuarios/{$usuario->id}/toggle-activo")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $usuario->id, 'activo' => true]);
    }

    public function test_admin_puede_desactivar_usuario_activo(): void
    {
        $usuario = User::factory()->capacitador()->create(['activo' => true]);

        $this->actingAs($this->admin)
            ->patch("/admin/usuarios/{$usuario->id}/toggle-activo")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $usuario->id, 'activo' => false]);
    }

    public function test_admin_no_puede_desactivarse_a_si_mismo(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/usuarios/{$this->admin->id}/toggle-activo")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'activo' => true]);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_puede_eliminar_otro_usuario(): void
    {
        $usuario = User::factory()->capacitador()->create();

        $this->actingAs($this->admin)->delete("/admin/usuarios/{$usuario->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }

    public function test_admin_no_puede_eliminarse_a_si_mismo(): void
    {
        $this->actingAs($this->admin)
            ->delete("/admin/usuarios/{$this->admin->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}
