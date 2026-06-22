<?php

namespace Tests\Feature\Public;

use App\Models\Certificado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_verificar_page_renders(): void
    {
        $this->get('/verificar')->assertStatus(200);
    }

    public function test_verificar_valida_codigo_requerido(): void
    {
        $this->post('/verificar', [])
            ->assertSessionHasErrors('codigo');
    }

    public function test_verificar_con_codigo_valido_muestra_certificado(): void
    {
        $certificado = Certificado::factory()->create(['codigo_unico' => 'EDCSST-2025-00001']);

        $response = $this->post('/verificar', ['codigo' => 'EDCSST-2025-00001']);

        $response->assertStatus(200);
        $response->assertViewHas('certificado', fn ($c) => $c->id === $certificado->id);
        $response->assertViewHas('verificacionRealizada', true);
    }

    public function test_verificar_con_codigo_en_minusculas_lo_normaliza(): void
    {
        Certificado::factory()->create(['codigo_unico' => 'EDCSST-2025-00002']);

        $response = $this->post('/verificar', ['codigo' => 'edcsst-2025-00002']);

        $response->assertStatus(200);
        $response->assertViewHas('certificado', fn ($c) => $c !== null);
    }

    public function test_verificar_con_codigo_inexistente_devuelve_null(): void
    {
        $response = $this->post('/verificar', ['codigo' => 'EDCSST-2099-99999']);

        $response->assertStatus(200);
        $response->assertViewHas('certificado', null);
    }

    public function test_certificado_inactivo_no_se_verifica(): void
    {
        Certificado::factory()->inactivo()->create(['codigo_unico' => 'EDCSST-2025-00099']);

        $response = $this->post('/verificar', ['codigo' => 'EDCSST-2025-00099']);

        $response->assertStatus(200);
        $response->assertViewHas('certificado', null);
    }

    public function test_verificar_indica_si_certificado_esta_vencido(): void
    {
        Certificado::factory()->vencido()->create(['codigo_unico' => 'EDCSST-2022-00001']);

        $response = $this->post('/verificar', ['codigo' => 'EDCSST-2022-00001']);

        $response->assertStatus(200);
        $response->assertViewHas('vencido', true);
    }
}
