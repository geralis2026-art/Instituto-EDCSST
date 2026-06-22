<?php

namespace Tests\Feature\Public;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_nosotros_page_renders(): void
    {
        $this->get('/nosotros')->assertStatus(200);
    }

    public function test_home_muestra_cursos_destacados(): void
    {
        Curso::factory()->destacado()->create(['nombre' => 'Trabajo en Alturas Destacado']);

        $this->get('/')->assertSee('Trabajo en Alturas Destacado');
    }

    public function test_home_no_muestra_cursos_inactivos(): void
    {
        Curso::factory()->inactivo()->create(['nombre' => 'Curso Oculto Inactivo', 'destacado' => true]);

        $this->get('/')->assertDontSee('Curso Oculto Inactivo');
    }

    public function test_home_no_muestra_cursos_no_destacados(): void
    {
        Curso::factory()->create(['nombre' => 'Curso Sin Destacar', 'destacado' => false]);

        $this->get('/')->assertDontSee('Curso Sin Destacar');
    }
}
