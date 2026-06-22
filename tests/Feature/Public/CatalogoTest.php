<?php

namespace Tests\Feature\Public;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogo_page_renders(): void
    {
        $this->get('/cursos')->assertStatus(200);
    }

    public function test_cursos_activos_aparecen_en_catalogo(): void
    {
        Curso::factory()->create(['nombre' => 'Espacios Confinados Nivel 1']);

        $this->get('/cursos')->assertSee('Espacios Confinados Nivel 1');
    }

    public function test_cursos_inactivos_no_aparecen_en_catalogo(): void
    {
        Curso::factory()->inactivo()->create(['nombre' => 'Curso Desactivado Secreto']);

        $this->get('/cursos')->assertDontSee('Curso Desactivado Secreto');
    }
}
