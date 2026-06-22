<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoTest extends TestCase
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

    private function datosValidos(int $categoriaId): array
    {
        return [
            'categoria_id'       => $categoriaId,
            'nombre'             => 'Curso de Prueba Único ' . uniqid(),
            'descripcion_corta'  => 'Descripción del curso de prueba para validación.',
            'duracion'           => '16 horas',
            'intensidad_horaria' => 16,
            'destacado'          => false,
            'activo'             => true,
        ];
    }

    // ── Acceso por rol ────────────────────────────────────────────────────────

    public function test_admin_puede_listar_cursos(): void
    {
        $this->actingAs($this->admin)->get('/admin/cursos')->assertStatus(200);
    }

    public function test_capacitador_no_puede_listar_cursos(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/cursos')->assertStatus(403);
    }

    public function test_capacitador_no_puede_crear_cursos(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/cursos/create')->assertStatus(403);
        $this->actingAs($this->capacitador)->post('/admin/cursos', ['nombre' => 'Test'])->assertStatus(403);
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_index_muestra_cursos_existentes(): void
    {
        Curso::factory()->create(['nombre' => 'Espacios Confinados Avanzado']);

        $this->actingAs($this->admin)->get('/admin/cursos')->assertSee('Espacios Confinados Avanzado');
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->admin)->get('/admin/cursos/create')->assertStatus(200);
    }

    public function test_admin_puede_crear_curso(): void
    {
        $categoria = Categoria::factory()->create();

        $this->actingAs($this->admin)->post('/admin/cursos', $this->datosValidos($categoria->id))
            ->assertRedirect();

        $this->assertDatabaseHas('cursos', ['categoria_id' => $categoria->id]);
    }

    public function test_store_valida_campos_requeridos(): void
    {
        $this->actingAs($this->admin)->post('/admin/cursos', [])
            ->assertSessionHasErrors(['categoria_id', 'nombre', 'descripcion_corta', 'duracion', 'intensidad_horaria']);
    }

    public function test_store_valida_categoria_existente(): void
    {
        $this->actingAs($this->admin)->post('/admin/cursos', [
            'categoria_id'       => 999999,
            'nombre'             => 'Test',
            'descripcion_corta'  => 'Test',
            'duracion'           => '8 horas',
            'intensidad_horaria' => 8,
        ])->assertSessionHasErrors('categoria_id');
    }

    public function test_store_valida_intensidad_horaria_positiva(): void
    {
        $categoria = Categoria::factory()->create();

        $this->actingAs($this->admin)->post('/admin/cursos', array_merge($this->datosValidos($categoria->id), [
            'intensidad_horaria' => 0,
        ]))->assertSessionHasErrors('intensidad_horaria');
    }

    public function test_slug_se_genera_automaticamente(): void
    {
        $categoria = Categoria::factory()->create();
        $nombre    = 'Trabajo en Alturas Especial ' . uniqid();

        $this->actingAs($this->admin)->post('/admin/cursos', array_merge($this->datosValidos($categoria->id), [
            'nombre' => $nombre,
        ]));

        $slug = \Illuminate\Support\Str::slug($nombre);
        $this->assertDatabaseHas('cursos', ['slug' => $slug]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_admin_puede_ver_detalle_del_curso(): void
    {
        $curso = Curso::factory()->create();
        $this->actingAs($this->admin)->get("/admin/cursos/{$curso->id}")->assertStatus(200);
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_edicion(): void
    {
        $curso = Curso::factory()->create();
        $this->actingAs($this->admin)->get("/admin/cursos/{$curso->id}/edit")->assertStatus(200);
    }

    public function test_admin_puede_actualizar_curso(): void
    {
        $curso     = Curso::factory()->create();
        $categoria = Categoria::factory()->create();

        $this->actingAs($this->admin)->put("/admin/cursos/{$curso->id}", [
            'categoria_id'       => $categoria->id,
            'nombre'             => 'Nombre Actualizado del Curso',
            'descripcion_corta'  => 'Descripción actualizada para las pruebas.',
            'duracion'           => '8 horas',
            'intensidad_horaria' => 8,
        ])->assertRedirect();

        $this->assertDatabaseHas('cursos', ['nombre' => 'Nombre Actualizado del Curso']);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_puede_eliminar_curso_sin_certificados(): void
    {
        $curso = Curso::factory()->create();

        $this->actingAs($this->admin)->delete("/admin/cursos/{$curso->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('cursos', ['id' => $curso->id]);
    }

    public function test_no_se_puede_eliminar_curso_con_certificados_asociados(): void
    {
        $cert  = Certificado::factory()->create();
        $curso = $cert->curso;

        // Con restrictOnDelete, intentar eliminar debe lanzar una excepción de FK
        // El controlador debería capturarla y redirigir con error
        $this->actingAs($this->admin)->delete("/admin/cursos/{$curso->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('cursos', ['id' => $curso->id]);
    }
}
