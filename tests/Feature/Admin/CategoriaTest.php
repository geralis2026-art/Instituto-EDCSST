<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaTest extends TestCase
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

    public function test_admin_puede_listar_categorias(): void
    {
        $this->actingAs($this->admin)->get('/admin/categorias')->assertStatus(200);
    }

    public function test_capacitador_no_puede_listar_categorias(): void
    {
        $this->actingAs($this->capacitador)->get('/admin/categorias')->assertStatus(403);
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function test_index_muestra_categorias_existentes(): void
    {
        Categoria::factory()->create(['nombre' => 'Categoría Visible Test']);

        $this->actingAs($this->admin)->get('/admin/categorias')
            ->assertSee('Categoría Visible Test');
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_creacion(): void
    {
        $this->actingAs($this->admin)->get('/admin/categorias/create')->assertStatus(200);
    }

    public function test_admin_puede_crear_categoria(): void
    {
        $this->actingAs($this->admin)->post('/admin/categorias', [
            'nombre'      => 'Seguridad Industrial',
            'descripcion' => 'Cursos relacionados con seguridad en el trabajo.',
            'activo'      => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('categorias', ['nombre' => 'Seguridad Industrial']);
    }

    public function test_store_genera_slug_automaticamente(): void
    {
        $this->actingAs($this->admin)->post('/admin/categorias', [
            'nombre' => 'Trabajo en Altura',
            'activo' => true,
        ]);

        $this->assertDatabaseHas('categorias', ['slug' => 'trabajo-en-altura']);
    }

    public function test_store_valida_nombre_requerido(): void
    {
        $this->actingAs($this->admin)->post('/admin/categorias', [])
            ->assertSessionHasErrors('nombre');
    }

    public function test_store_valida_nombre_unico(): void
    {
        Categoria::factory()->create(['nombre' => 'Categoría Duplicada', 'slug' => 'categoria-duplicada']);

        $this->actingAs($this->admin)->post('/admin/categorias', [
            'nombre' => 'Categoría Duplicada',
        ])->assertSessionHasErrors('slug');
    }

    public function test_capacitador_no_puede_crear_categoria(): void
    {
        $this->actingAs($this->capacitador)->post('/admin/categorias', [
            'nombre' => 'Test',
        ])->assertStatus(403);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function test_admin_puede_ver_detalle_de_categoria(): void
    {
        $categoria = Categoria::factory()->create();
        $this->actingAs($this->admin)->get("/admin/categorias/{$categoria->id}")->assertStatus(200);
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function test_admin_puede_ver_formulario_de_edicion(): void
    {
        $categoria = Categoria::factory()->create();
        $this->actingAs($this->admin)->get("/admin/categorias/{$categoria->id}/edit")->assertStatus(200);
    }

    public function test_admin_puede_actualizar_categoria(): void
    {
        $categoria = Categoria::factory()->create();

        $this->actingAs($this->admin)->put("/admin/categorias/{$categoria->id}", [
            'nombre' => 'Nombre de Categoría Actualizado',
            'activo' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('categorias', ['nombre' => 'Nombre de Categoría Actualizado']);
    }

    public function test_admin_puede_desactivar_categoria(): void
    {
        $categoria = Categoria::factory()->create(['activo' => true]);

        $this->actingAs($this->admin)->put("/admin/categorias/{$categoria->id}", [
            'nombre' => $categoria->nombre,
            'activo' => false,
        ])->assertRedirect();

        $this->assertDatabaseHas('categorias', ['id' => $categoria->id, 'activo' => false]);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_puede_eliminar_categoria_sin_cursos(): void
    {
        $categoria = Categoria::factory()->create();

        $this->actingAs($this->admin)->delete("/admin/categorias/{$categoria->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_no_se_puede_eliminar_categoria_con_cursos_asociados(): void
    {
        $curso     = Curso::factory()->create();
        $categoria = $curso->categoria;

        // Con restrictOnDelete, el controlador debe capturar la FK y redirigir con error
        $this->actingAs($this->admin)->delete("/admin/categorias/{$categoria->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }
}
