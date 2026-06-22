<?php

namespace Tests\Feature\Admin;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MensajeTest extends TestCase
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

    public function test_admin_puede_listar_mensajes(): void
    {
        $this->actingAs($this->admin)->get('/admin/mensajes')->assertStatus(200);
    }

    public function test_capacitador_no_puede_listar_mensajes(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/mensajes')->assertStatus(403);
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_index_muestra_mensajes_recibidos(): void
    {
        Mensaje::factory()->create(['nombre' => 'Remitente Visible Test']);

        $this->actingAs($this->admin)->get('/admin/mensajes')
            ->assertSee('Remitente Visible Test');
    }

    public function test_index_filtra_por_estado(): void
    {
        Mensaje::factory()->create(['nombre' => 'Mensaje Nuevo']);
        Mensaje::factory()->respondido()->create(['nombre' => 'Mensaje Respondido']);

        $this->actingAs($this->admin)->get('/admin/mensajes?estado=nuevo')
            ->assertSee('Mensaje Nuevo')
            ->assertDontSee('Mensaje Respondido');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_admin_puede_ver_mensaje(): void
    {
        $mensaje = Mensaje::factory()->create();
        $this->actingAs($this->admin)->get("/admin/mensajes/{$mensaje->id}")->assertStatus(200);
    }

    public function test_ver_mensaje_nuevo_lo_marca_como_leido(): void
    {
        $mensaje = Mensaje::factory()->create(['estado' => Mensaje::ESTADO_NUEVO]);

        $this->actingAs($this->admin)->get("/admin/mensajes/{$mensaje->id}");

        $this->assertDatabaseHas('mensajes', ['id' => $mensaje->id, 'estado' => Mensaje::ESTADO_LEIDO]);
    }

    public function test_ver_mensaje_ya_leido_no_cambia_su_estado(): void
    {
        $mensaje = Mensaje::factory()->leido()->create();

        $this->actingAs($this->admin)->get("/admin/mensajes/{$mensaje->id}");

        $this->assertDatabaseHas('mensajes', ['id' => $mensaje->id, 'estado' => Mensaje::ESTADO_LEIDO]);
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function test_admin_puede_marcar_mensaje_como_respondido(): void
    {
        $mensaje = Mensaje::factory()->create();

        $this->actingAs($this->admin)->put("/admin/mensajes/{$mensaje->id}", [
            'estado'         => Mensaje::ESTADO_RESPONDIDO,
            'notas_internas' => 'Se respondió por correo el día de hoy.',
        ])->assertRedirect();

        $this->assertDatabaseHas('mensajes', ['id' => $mensaje->id, 'estado' => Mensaje::ESTADO_RESPONDIDO]);
    }

    public function test_update_valida_estado_valido(): void
    {
        $mensaje = Mensaje::factory()->create();

        $this->actingAs($this->admin)->put("/admin/mensajes/{$mensaje->id}", [
            'estado' => 'estado-invalido',
        ])->assertSessionHasErrors('estado');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_puede_eliminar_mensaje(): void
    {
        $mensaje = Mensaje::factory()->create();

        $this->actingAs($this->admin)->delete("/admin/mensajes/{$mensaje->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('mensajes', ['id' => $mensaje->id]);
    }
}
