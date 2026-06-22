<?php

namespace Tests\Feature\Admin;

use App\Models\Capacitado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapacitadoTest extends TestCase
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

    // ── Index y búsqueda ──────────────────────────────────────────────────────

    public function test_admin_puede_listar_capacitados(): void
    {
        $this->actingAs($this->admin)->get('/admin/capacitados')->assertStatus(200);
    }

    public function test_capacitador_puede_listar_capacitados(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/capacitados')->assertStatus(200);
    }

    public function test_index_muestra_capacitados_existentes(): void
    {
        Capacitado::factory()->create(['nombre_completo' => 'María Visible García']);

        $this->actingAs($this->admin)
            ->get('/admin/capacitados')
            ->assertSee('María Visible García');
    }

    public function test_busqueda_por_nombre_retorna_resultados(): void
    {
        Capacitado::factory()->create(['nombre_completo' => 'Carlos Único Ramírez']);

        $this->actingAs($this->admin)
            ->get('/admin/capacitados?busqueda=Carlos+Único')
            ->assertStatus(200)
            ->assertSee('Carlos Único Ramírez');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_admin_puede_ver_detalle_de_capacitado(): void
    {
        $capacitado = Capacitado::factory()->create();
        $this->actingAs($this->admin)->get("/admin/capacitados/{$capacitado->id}")->assertStatus(200);
    }

    public function test_capacitador_puede_ver_detalle_de_capacitado(): void
    {
        $capacitado = Capacitado::factory()->create();
        $this->actingAs($this->capacitador)->get("/admin/capacitados/{$capacitado->id}")->assertStatus(200);
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->admin)->get('/admin/capacitados/create')->assertStatus(200);
    }

    public function test_capacitador_no_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/capacitados/create')->assertStatus(403);
    }

    public function test_admin_puede_crear_capacitado(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/capacitados', [
            'nombre_completo' => 'Carlos Pérez González',
            'documento'       => '1234567890',
            'correo'          => 'carlos@ejemplo.com',
            'telefono'        => '3001234567',
            'rh'              => 'O+',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('capacitados', ['documento' => '1234567890']);
    }

    public function test_store_valida_nombre_y_documento_requeridos(): void
    {
        $this->actingAs($this->admin)->post('/admin/capacitados', [])
            ->assertSessionHasErrors(['nombre_completo', 'documento']);
    }

    public function test_store_valida_documento_unico(): void
    {
        Capacitado::factory()->create(['documento' => '1111111111']);

        $this->actingAs($this->admin)->post('/admin/capacitados', [
            'nombre_completo' => 'Otro Nombre',
            'documento'       => '1111111111',
        ])->assertSessionHasErrors('documento');
    }

    public function test_store_valida_formato_de_correo(): void
    {
        $this->actingAs($this->admin)->post('/admin/capacitados', [
            'nombre_completo' => 'Nombre Test',
            'documento'       => '9876543210',
            'correo'          => 'no-es-email',
        ])->assertSessionHasErrors('correo');
    }

    public function test_capacitador_no_puede_crear_capacitado(): void
    {
        $this->actingAs($this->capacitador)->post('/admin/capacitados', [
            'nombre_completo' => 'Test',
            'documento'       => '9999999999',
        ])->assertStatus(403);
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_edicion(): void
    {
        $capacitado = Capacitado::factory()->create();
        $this->actingAs($this->admin)->get("/admin/capacitados/{$capacitado->id}/edit")->assertStatus(200);
    }

    public function test_capacitador_no_puede_ver_formulario_de_edicion(): void
    {
        $capacitado = Capacitado::factory()->create();
        $this->actingAs($this->capacitador)->get("/admin/capacitados/{$capacitado->id}/edit")->assertStatus(403);
    }

    public function test_admin_puede_actualizar_capacitado(): void
    {
        $capacitado = Capacitado::factory()->create();

        $this->actingAs($this->admin)->put("/admin/capacitados/{$capacitado->id}", [
            'nombre_completo' => 'Nombre Totalmente Actualizado',
            'documento'       => $capacitado->documento,
        ])->assertRedirect();

        $this->assertDatabaseHas('capacitados', ['nombre_completo' => 'Nombre Totalmente Actualizado']);
    }

    public function test_capacitador_no_puede_actualizar_capacitado(): void
    {
        $capacitado = Capacitado::factory()->create();

        $this->actingAs($this->capacitador)->put("/admin/capacitados/{$capacitado->id}", [
            'nombre_completo' => 'Intento de Modificar',
            'documento'       => $capacitado->documento,
        ])->assertStatus(403);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_puede_eliminar_capacitado(): void
    {
        $capacitado = Capacitado::factory()->create();

        $this->actingAs($this->admin)->delete("/admin/capacitados/{$capacitado->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('capacitados', ['id' => $capacitado->id]);
    }

    public function test_capacitador_no_puede_eliminar_capacitado(): void
    {
        $capacitado = Capacitado::factory()->create();

        $this->actingAs($this->capacitador)->delete("/admin/capacitados/{$capacitado->id}")
            ->assertStatus(403);
    }

    // ── Link de registro ──────────────────────────────────────────────────────

    public function test_admin_puede_generar_link_de_registro(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/capacitados/link-registro')
            ->assertStatus(200);
    }
}
