<?php

namespace Tests\Feature\Admin;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificadoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $capacitador;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('certificados');
        $this->admin       = User::factory()->admin()->create();
        $this->capacitador = User::factory()->capacitador()->create();
    }

    private function fakePdf(): UploadedFile
    {
        return UploadedFile::fake()->create('certificado.pdf', 100, 'application/pdf');
    }

    private function datosMinimos(Capacitado $cap, Curso $curso): array
    {
        return [
            'capacitado_id'      => $cap->id,
            'curso_id'           => $curso->id,
            'fecha_emision'      => '2025-01-15',
            'intensidad_horaria' => 40,
            'anios_vigencia'     => 1,
            'modalidad'          => 'presencial',
            'archivo_pdf'        => $this->fakePdf(),
        ];
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_admin_puede_listar_certificados(): void
    {
        $this->actingAs($this->admin)->get('/admin/certificados')->assertStatus(200);
    }

    public function test_capacitador_puede_listar_certificados(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/certificados')->assertStatus(200);
    }

    public function test_index_muestra_codigo_unico(): void
    {
        Certificado::factory()->create(['codigo_unico' => 'EDCSST-2025-00001']);

        $this->actingAs($this->admin)
            ->get('/admin/certificados')
            ->assertSee('EDCSST-2025-00001');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_admin_puede_ver_detalle_de_certificado(): void
    {
        $cert = Certificado::factory()->create();
        $this->actingAs($this->admin)->get("/admin/certificados/{$cert->id}")->assertStatus(200);
    }

    public function test_capacitador_puede_ver_detalle_de_certificado(): void
    {
        $cert = Certificado::factory()->create();
        $this->actingAs($this->capacitador)->get("/admin/certificados/{$cert->id}")->assertStatus(200);
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->admin)->get('/admin/certificados/create')->assertStatus(200);
    }

    public function test_capacitador_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/certificados/create')->assertStatus(200);
    }

    public function test_admin_puede_crear_certificado(): void
    {
        $cap  = Capacitado::factory()->create();
        $cur  = Curso::factory()->create();

        $this->actingAs($this->admin)->post('/admin/certificados', $this->datosMinimos($cap, $cur))
            ->assertRedirect();

        $this->assertDatabaseHas('certificados', ['capacitado_id' => $cap->id, 'curso_id' => $cur->id]);
    }

    public function test_capacitador_puede_crear_certificado(): void
    {
        $cap = Capacitado::factory()->create();
        $cur = Curso::factory()->create();

        $this->actingAs($this->capacitador)->post('/admin/certificados', $this->datosMinimos($cap, $cur))
            ->assertRedirect();
    }

    public function test_store_valida_campos_requeridos(): void
    {
        $this->actingAs($this->admin)->post('/admin/certificados', [])
            ->assertSessionHasErrors(['capacitado_id', 'curso_id', 'fecha_emision', 'intensidad_horaria', 'anios_vigencia']);
    }

    public function test_store_valida_capacitado_existente(): void
    {
        $cur = Curso::factory()->create();

        $this->actingAs($this->admin)->post('/admin/certificados', [
            'capacitado_id'      => 999999,
            'curso_id'           => $cur->id,
            'fecha_emision'      => '2025-01-01',
            'intensidad_horaria' => 40,
            'anios_vigencia'     => 1,
            'archivo_pdf'        => $this->fakePdf(),
        ])->assertSessionHasErrors('capacitado_id');
    }

    public function test_store_valida_anios_vigencia_en_rango(): void
    {
        $cap = Capacitado::factory()->create();
        $cur = Curso::factory()->create();

        $this->actingAs($this->admin)->post('/admin/certificados', array_merge($this->datosMinimos($cap, $cur), [
            'anios_vigencia' => 5,
        ]))->assertSessionHasErrors('anios_vigencia');
    }

    public function test_store_valida_codigo_unico(): void
    {
        Certificado::factory()->create(['codigo_unico' => 'EDCSST-2025-99999']);

        $cap = Capacitado::factory()->create();
        $cur = Curso::factory()->create();

        $this->actingAs($this->admin)->post('/admin/certificados', array_merge($this->datosMinimos($cap, $cur), [
            'codigo_unico' => 'EDCSST-2025-99999',
        ]))->assertSessionHasErrors('codigo_unico');
    }

    public function test_store_calcula_fecha_vencimiento_segun_anios(): void
    {
        $cap = Capacitado::factory()->create();
        $cur = Curso::factory()->create();

        $this->actingAs($this->admin)->post('/admin/certificados', array_merge($this->datosMinimos($cap, $cur), [
            'fecha_emision'  => '2025-03-01',
            'anios_vigencia' => 2,
        ]));

        $this->assertDatabaseHas('certificados', ['fecha_vencimiento' => '2027-03-01']);
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_edicion(): void
    {
        $cert = Certificado::factory()->create();
        $this->actingAs($this->admin)->get("/admin/certificados/{$cert->id}/edit")->assertStatus(200);
    }

    public function test_capacitador_no_puede_ver_formulario_de_edicion(): void
    {
        $cert = Certificado::factory()->create();
        $this->actingAs($this->capacitador)->get("/admin/certificados/{$cert->id}/edit")->assertStatus(403);
    }

    public function test_admin_puede_actualizar_certificado(): void
    {
        $cert = Certificado::factory()->create();
        $cap  = $cert->capacitado;
        $cur  = $cert->curso;

        $this->actingAs($this->admin)->put("/admin/certificados/{$cert->id}", array_merge(
            $this->datosMinimos($cap, $cur),
            ['intensidad_horaria' => 80]
        ))->assertRedirect();

        $this->assertDatabaseHas('certificados', ['id' => $cert->id, 'intensidad_horaria' => 80]);
    }

    public function test_capacitador_no_puede_actualizar_certificado(): void
    {
        $cert = Certificado::factory()->create();

        $this->actingAs($this->capacitador)->put("/admin/certificados/{$cert->id}", [
            'capacitado_id'      => $cert->capacitado_id,
            'curso_id'           => $cert->curso_id,
            'fecha_emision'      => '2025-01-01',
            'intensidad_horaria' => 40,
            'anios_vigencia'     => 1,
        ])->assertStatus(403);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_puede_eliminar_certificado(): void
    {
        $cert = Certificado::factory()->create();

        $this->actingAs($this->admin)->delete("/admin/certificados/{$cert->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('certificados', ['id' => $cert->id]);
    }

    public function test_capacitador_no_puede_eliminar_certificado(): void
    {
        $cert = Certificado::factory()->create();

        $this->actingAs($this->capacitador)->delete("/admin/certificados/{$cert->id}")
            ->assertStatus(403);
    }

    // ── Toggle activo ─────────────────────────────────────────────────────────

    public function test_admin_puede_desactivar_certificado(): void
    {
        $cert = Certificado::factory()->create(['activo' => true]);

        $this->actingAs($this->admin)->patch("/admin/certificados/{$cert->id}/toggle-activo")
            ->assertRedirect();

        $this->assertDatabaseHas('certificados', ['id' => $cert->id, 'activo' => false]);
    }

    public function test_admin_puede_reactivar_certificado(): void
    {
        $cert = Certificado::factory()->inactivo()->create();

        $this->actingAs($this->admin)->patch("/admin/certificados/{$cert->id}/toggle-activo")
            ->assertRedirect();

        $this->assertDatabaseHas('certificados', ['id' => $cert->id, 'activo' => true]);
    }

    public function test_capacitador_no_puede_toggle_estado_de_certificado(): void
    {
        $cert = Certificado::factory()->create();

        $this->actingAs($this->capacitador)
            ->patch("/admin/certificados/{$cert->id}/toggle-activo")
            ->assertStatus(403);
    }
}
