<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfiguracionTest extends TestCase
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

    public function test_admin_puede_ver_configuracion(): void
    {
        $this->actingAs($this->admin)->get('/admin/configuracion')->assertStatus(200);
    }

    public function test_capacitador_no_puede_ver_configuracion(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/configuracion')->assertStatus(403);
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_admin_puede_actualizar_configuracion(): void
    {
        $this->actingAs($this->admin)->put('/admin/configuracion', [
            'nombre_instituto' => 'Instituto EDCSST Actualizado',
            'telefono'         => '+57 321 9999999',
            'correo_contacto'  => 'nuevo@edcsst.com',
            'direccion'        => 'Medellín, Colombia',
        ])->assertRedirect(route('admin.configuracion.edit'));

        $this->assertDatabaseHas('configuracion_sitio', [
            'nombre_instituto' => 'Instituto EDCSST Actualizado',
        ]);
    }

    public function test_update_valida_nombre_instituto_requerido(): void
    {
        $this->actingAs($this->admin)->put('/admin/configuracion', [
            'nombre_instituto' => '',
        ])->assertSessionHasErrors('nombre_instituto');
    }

    public function test_update_valida_formato_de_correo(): void
    {
        $this->actingAs($this->admin)->put('/admin/configuracion', [
            'nombre_instituto' => 'Instituto EDCSST',
            'correo_contacto'  => 'no-es-un-email',
        ])->assertSessionHasErrors('correo_contacto');
    }

    public function test_update_valida_url_de_facebook(): void
    {
        $this->actingAs($this->admin)->put('/admin/configuracion', [
            'nombre_instituto' => 'Instituto EDCSST',
            'facebook'         => 'no-es-una-url',
        ])->assertSessionHasErrors('facebook');
    }

    public function test_update_valida_url_de_instagram(): void
    {
        $this->actingAs($this->admin)->put('/admin/configuracion', [
            'nombre_instituto' => 'Instituto EDCSST',
            'instagram'        => 'instagram.com/sin-https',
        ])->assertSessionHasErrors('instagram');
    }

    public function test_capacitador_no_puede_actualizar_configuracion(): void
    {
        $this->actingAs($this->capacitador)->put('/admin/configuracion', [
            'nombre_instituto' => 'Intento de Hack',
        ])->assertStatus(403);
    }
}
