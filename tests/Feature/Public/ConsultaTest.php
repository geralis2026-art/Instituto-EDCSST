<?php

namespace Tests\Feature\Public;

use App\Models\Capacitado;
use App\Models\Certificado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    use RefreshDatabase;

    public function test_consulta_page_renders(): void
    {
        $this->get('/consulta')->assertStatus(200);
    }

    public function test_busqueda_valida_campos_requeridos(): void
    {
        $this->post('/consulta', [])
            ->assertSessionHasErrors(['tipo_busqueda', 'valor']);
    }

    public function test_busqueda_valida_tipo_busqueda(): void
    {
        $this->post('/consulta', [
            'tipo_busqueda' => 'invalido',
            'valor'         => '123',
        ])->assertSessionHasErrors('tipo_busqueda');
    }

    public function test_buscar_por_documento_encuentra_certificados(): void
    {
        $capacitado = Capacitado::factory()->create(['documento' => '1234567890']);
        Certificado::factory()->create(['capacitado_id' => $capacitado->id]);

        $response = $this->post('/consulta', [
            'tipo_busqueda' => 'documento',
            'valor'         => '1234567890',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('certificados', fn ($c) => $c->count() === 1);
    }

    public function test_buscar_por_documento_inexistente_muestra_error(): void
    {
        $response = $this->post('/consulta', [
            'tipo_busqueda' => 'documento',
            'valor'         => '9999999999',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('mensajeError');
    }

    public function test_buscar_por_codigo_unico_encuentra_certificado(): void
    {
        Certificado::factory()->create(['codigo_unico' => 'EDCSST-2025-00001']);

        $response = $this->post('/consulta', [
            'tipo_busqueda' => 'codigo',
            'valor'         => 'EDCSST-2025-00001',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('certificados', fn ($c) => $c->count() === 1);
    }

    public function test_buscar_por_codigo_inexistente_muestra_error(): void
    {
        $response = $this->post('/consulta', [
            'tipo_busqueda' => 'codigo',
            'valor'         => 'EDCSST-2099-99999',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('mensajeError');
    }

    public function test_certificados_inactivos_no_aparecen_en_busqueda(): void
    {
        $capacitado = Capacitado::factory()->create(['documento' => '5555555555']);
        Certificado::factory()->inactivo()->create(['capacitado_id' => $capacitado->id]);

        $response = $this->post('/consulta', [
            'tipo_busqueda' => 'documento',
            'valor'         => '5555555555',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('mensajeError');
    }

    public function test_url_firmada_de_descarga_requiere_firma_valida(): void
    {
        $certificado = Certificado::factory()->create();

        // Sin firma → debe fallar
        $this->get("/consulta/descargar/{$certificado->id}")
            ->assertStatus(403);
    }
}
