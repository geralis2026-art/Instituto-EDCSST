<?php

namespace Tests\Feature\Public;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

    /**
     * Capacitado y Certificado tienen PropietarioScope. Si un instructor tiene
     * sesión activa en el mismo navegador donde un capacitado consulta sus
     * certificados, la búsqueda no debe verse afectada por esa sesión (bug
     * real: devolvía "no encontrado" para documentos/códigos válidos que
     * pertenecían a otro dueño).
     */
    public function test_buscar_por_documento_encuentra_certificados_con_instructor_logueado(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $capacitado = Capacitado::factory()->create([
            'documento' => '1234567890',
            'user_id'   => $edna->id,
        ]);
        Certificado::factory()->create([
            'capacitado_id' => $capacitado->id,
            'user_id'       => $edna->id,
        ]);

        $response = $this->actingAs($mauricio)->post('/consulta', [
            'tipo_busqueda' => 'documento',
            'valor'         => '1234567890',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('certificados', fn ($c) => $c->count() === 1);
    }

    public function test_buscar_por_codigo_encuentra_certificado_con_instructor_logueado(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        Certificado::factory()->create([
            'codigo_unico' => 'EDCSST-2025-00077',
            'user_id'      => $edna->id,
        ]);

        $response = $this->actingAs($mauricio)->post('/consulta', [
            'tipo_busqueda' => 'codigo',
            'valor'         => 'EDCSST-2025-00077',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('certificados', fn ($c) => $c->count() === 1);
    }

    public function test_descargar_funciona_con_instructor_logueado_sobre_certificado_ajeno(): void
    {
        Storage::fake('certificados');

        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        $certificado = Certificado::factory()->create([
            'user_id'            => $edna->id,
            'archivo_pdf'        => 'certificados/prueba.pdf',
            'fecha_vencimiento'  => now()->addYear(),
        ]);
        Storage::disk('certificados')->put('certificados/prueba.pdf', 'contenido-pdf-de-prueba');

        $url = URL::temporarySignedRoute('consulta.descargar', now()->addMinutes(30), $certificado);

        $this->actingAs($mauricio)->get($url)->assertStatus(200);
    }
}
