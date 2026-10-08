<?php

namespace Tests\Feature\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Páginas de error, vista previa al compartir, robots.txt y pantalla de acceso. */
class PaginasPublicasTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_404_esta_en_espanol_con_enlaces_utiles(): void
    {
        $this->get('/esta-pagina-no-existe')
            ->assertStatus(404)
            ->assertSee('No encontramos esa página')
            ->assertSee('Ir al inicio')
            ->assertSee('Verificar un certificado')
            ->assertSee('noindex');
    }

    public function test_la_pagina_403_esta_en_espanol(): void
    {
        $capacitador = User::factory()->capacitador()->create();

        $this->actingAs($capacitador)->get('/admin/usuarios')
            ->assertStatus(403)
            ->assertSee('No tienes acceso a esta página');
    }

    public function test_la_pagina_429_esta_en_espanol_e_indica_cuanto_esperar(): void
    {
        // Los límites públicos (consulta, verificación, contacto) redirigen con un mensaje en el mismo
        // formulario; esta página cubre cualquier otro 429 que llegue a mostrarse.
        Route::get('/_prueba-429', fn () => abort(429, '', ['Retry-After' => 90]));

        $this->get('/_prueba-429')
            ->assertStatus(429)
            ->assertSee('Demasiados intentos seguidos')
            ->assertSee('2 minutos');
    }

    public function test_la_pagina_429_sin_tiempo_de_espera_no_falla(): void
    {
        Route::get('/_prueba-429-b', fn () => abort(429));

        $this->get('/_prueba-429-b')
            ->assertStatus(429)
            ->assertSee('Demasiados intentos seguidos');
    }

    public function test_la_pagina_500_esta_en_espanol(): void
    {
        Route::get('/_prueba-500', fn () => throw new \RuntimeException('falla de prueba'));

        $this->get('/_prueba-500')
            ->assertStatus(500)
            ->assertSee('Algo salió mal de nuestro lado')
            ->assertDontSee('falla de prueba');
    }

    public function test_el_sitio_publico_incluye_vista_previa_para_compartir(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:description"', false)
            ->assertSee('img/og-edcsst-escudo.png', false)
            ->assertSee('name="twitter:card" content="summary"', false)
            ->assertSee('rel="canonical"', false);
    }

    public function test_la_imagen_de_vista_previa_es_cuadrada_y_liviana(): void
    {
        $ruta = public_path('img/og-edcsst-escudo.png');

        $this->assertFileExists($ruta);
        $this->assertLessThan(300 * 1024, filesize($ruta));
        $this->assertSame([512, 512], array_slice(getimagesize($ruta), 0, 2));
    }

    public function test_robots_bloquea_las_zonas_privadas(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        foreach (['/admin', '/login', '/aula', '/registro/', '/profile'] as $ruta) {
            $this->assertStringContainsString("Disallow: {$ruta}", $robots);
        }
    }

    public function test_el_login_tiene_el_diseno_nuevo_y_no_se_indexa(): void
    {
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Iniciar sesión')
            ->assertSee('Mostrar contraseña')
            ->assertSee('Saltar al formulario')
            ->assertSee('noindex')
            ->assertSee('¿Olvidaste tu contraseña?');
    }

    public function test_la_recuperacion_de_contrasena_tiene_su_propio_titulo(): void
    {
        $this->get('/forgot-password')
            ->assertStatus(200)
            ->assertSee('Recuperar contraseña')
            ->assertDontSee('Ingresa tus credenciales para acceder al panel');
    }
}
