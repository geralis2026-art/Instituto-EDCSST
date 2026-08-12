<?php

namespace Tests\Feature\Admin;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_la_seccion_de_mensajes_en_el_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        Mensaje::factory()->create(['nombre' => 'Juan Contacto']);

        $this->actingAs($admin)->get('/admin')
            ->assertStatus(200)
            ->assertSee('Mensajes nuevos')
            ->assertSee('Juan Contacto');
    }

    public function test_capacitador_no_ve_la_seccion_de_mensajes_en_el_dashboard(): void
    {
        $capacitador = User::factory()->capacitador()->create();
        Mensaje::factory()->create(['nombre' => 'Juan Contacto']);

        $this->actingAs($capacitador)->get('/admin')
            ->assertStatus(200)
            ->assertDontSee('Mensajes nuevos')
            ->assertDontSee('Juan Contacto');
    }

    public function test_instructor_no_ve_la_seccion_de_mensajes_en_el_dashboard(): void
    {
        $instructor = User::factory()->instructor()->create();
        Mensaje::factory()->create(['nombre' => 'Juan Contacto']);

        $this->actingAs($instructor)->get('/admin')
            ->assertStatus(200)
            ->assertDontSee('Mensajes nuevos')
            ->assertDontSee('Juan Contacto');
    }

    public function test_el_cache_de_estadisticas_no_se_mezcla_entre_usuarios(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        \App\Models\Capacitado::factory()->create(['user_id' => $edna->id]);
        \App\Models\Capacitado::factory()->create(['user_id' => $mauricio->id]);

        // Mauricio entra primero: su vista del dashboard queda cacheada bajo su propia clave.
        $this->actingAs($mauricio)->get('/admin');

        // Edna entra después, dentro de la misma ventana de caché: debe ver el total (2),
        // no el número scoped que vio Mauricio (1), porque la clave de caché es por usuario.
        $response = $this->actingAs($edna)->get('/admin');

        $response->assertStatus(200);
        $this->assertSame(2, $response->viewData('totalCapacitados'));
    }
}
