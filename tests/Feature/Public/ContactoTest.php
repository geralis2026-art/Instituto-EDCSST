<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContactoTest extends TestCase
{
    use RefreshDatabase;

    public function test_contacto_page_renders(): void
    {
        $this->get('/contacto')->assertStatus(200);
    }

    public function test_contacto_valida_campos_requeridos(): void
    {
        $this->post('/contacto', [])
            ->assertSessionHasErrors(['nombre', 'correo', 'mensaje', 'g-recaptcha-response']);
    }

    public function test_contacto_valida_longitud_minima_de_mensaje(): void
    {
        $this->post('/contacto', [
            'nombre'               => 'Test',
            'correo'               => 'test@ejemplo.com',
            'mensaje'              => 'Corto',
            'g-recaptcha-response' => 'token',
        ])->assertSessionHasErrors('mensaje');
    }

    public function test_contacto_valida_formato_de_correo(): void
    {
        $this->post('/contacto', [
            'nombre'               => 'Test',
            'correo'               => 'no-es-email',
            'mensaje'              => 'Este es un mensaje lo suficientemente largo.',
            'g-recaptcha-response' => 'token',
        ])->assertSessionHasErrors('correo');
    }

    public function test_contacto_guarda_mensaje_con_captcha_valido(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $response = $this->post('/contacto', [
            'nombre'               => 'Juan Pérez',
            'correo'               => 'juan@ejemplo.com',
            'mensaje'              => 'Este es mi mensaje de consulta con suficientes caracteres.',
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response->assertRedirect(route('contacto'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('mensajes', ['correo' => 'juan@ejemplo.com', 'nombre' => 'Juan Pérez']);
    }

    public function test_contacto_rechaza_captcha_fallido(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false], 200),
        ]);

        $this->post('/contacto', [
            'nombre'               => 'Juan Pérez',
            'correo'               => 'juan@ejemplo.com',
            'mensaje'              => 'Mensaje de prueba con caracteres suficientes.',
            'g-recaptcha-response' => 'invalid-token',
        ])->assertSessionHasErrors('g-recaptcha-response');

        $this->assertDatabaseMissing('mensajes', ['correo' => 'juan@ejemplo.com']);
    }

    public function test_contacto_maneja_fallo_de_conexion_con_google(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::sequence()->whenEmpty(
                Http::response([], 500)
            ),
        ]);

        // Simulamos excepción de conexión
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Timeout');
        });

        $response = $this->post('/contacto', [
            'nombre'               => 'Test',
            'correo'               => 'test@ejemplo.com',
            'mensaje'              => 'Mensaje de prueba con caracteres suficientes.',
            'g-recaptcha-response' => 'token',
        ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
    }
}
