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

    /**
     * Reproduce el bug reportado: el contador de "Capacitados" en el
     * dashboard quedaba desactualizado hasta 60 s después de borrar uno,
     * porque destroy() no invalidaba el caché de dashboard_stats.
     */
    public function test_el_contador_de_capacitados_se_actualiza_al_eliminar_uno(): void
    {
        $mauricio  = User::factory()->instructor()->create();
        $capacitado = \App\Models\Capacitado::factory()->create(['user_id' => $mauricio->id]);

        // Carga el dashboard primero para que quede cacheado con el conteo viejo.
        $antes = $this->actingAs($mauricio)->get('/admin');
        $this->assertSame(1, $antes->viewData('totalCapacitados'));

        $this->actingAs($mauricio)->delete("/admin/capacitados/{$capacitado->id}");

        $despues = $this->actingAs($mauricio)->get('/admin');
        $this->assertSame(0, $despues->viewData('totalCapacitados'));
    }

    public function test_el_dashboard_muestra_los_certificados_que_vencen_en_30_dias(): void
    {
        $admin = User::factory()->admin()->create();

        $porVencer = \App\Models\Certificado::factory()->create([
            'fecha_vencimiento' => today()->addDays(10)->toDateString(),
        ]);
        \App\Models\Certificado::factory()->create(['fecha_vencimiento' => today()->addDays(90)->toDateString()]);
        \App\Models\Certificado::factory()->vencido()->create();
        \App\Models\Certificado::factory()->inactivo()->create(['fecha_vencimiento' => today()->addDays(5)->toDateString()]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200)->assertSee('Próximos a vencer');
        $this->assertSame(1, $response->viewData('totalPorVencer'));
        $this->assertTrue($response->viewData('porVencer')->contains('id', $porVencer->id));
    }

    public function test_el_filtro_vence_30_solo_lista_certificados_por_vencer(): void
    {
        $admin = User::factory()->admin()->create();

        $proximo = \App\Models\Certificado::factory()->create(['fecha_vencimiento' => today()->addDays(3)->toDateString()]);
        $lejano  = \App\Models\Certificado::factory()->create(['fecha_vencimiento' => today()->addDays(200)->toDateString()]);

        $this->actingAs($admin)->get('/admin/certificados?vence=30')
            ->assertStatus(200)
            ->assertSee($proximo->codigo_unico)
            ->assertDontSee($lejano->codigo_unico)
            ->assertSee('Vence pronto');
    }

    public function test_un_instructor_solo_ve_por_vencer_sus_propios_certificados(): void
    {
        $edna     = User::factory()->admin()->create();
        $mauricio = User::factory()->instructor()->create();

        \App\Models\Certificado::factory()->create(['user_id' => $edna->id, 'fecha_vencimiento' => today()->addDays(5)->toDateString()]);
        \App\Models\Certificado::factory()->create(['user_id' => $mauricio->id, 'fecha_vencimiento' => today()->addDays(6)->toDateString()]);

        $response = $this->actingAs($mauricio)->get('/admin');

        $this->assertSame(1, $response->viewData('totalPorVencer'));
    }

    public function test_el_listado_de_certificados_filtra_por_estado(): void
    {
        $admin = User::factory()->admin()->create();

        $vigente  = \App\Models\Certificado::factory()->create(['fecha_vencimiento' => today()->addDays(120)->toDateString()]);
        $porVencer = \App\Models\Certificado::factory()->create(['fecha_vencimiento' => today()->addDays(8)->toDateString()]);
        $vencido  = \App\Models\Certificado::factory()->vencido()->create();
        $inactivo = \App\Models\Certificado::factory()->inactivo()->create(['fecha_vencimiento' => today()->addDays(200)->toDateString()]);

        $casos = [
            'vigentes'   => [[$vigente, $porVencer], [$vencido, $inactivo]],
            'por_vencer' => [[$porVencer], [$vigente, $vencido, $inactivo]],
            'vencidos'   => [[$vencido], [$vigente, $porVencer, $inactivo]],
            'inactivos'  => [[$inactivo], [$vigente, $porVencer, $vencido]],
        ];

        foreach ($casos as $estado => [$deben, $nodeben]) {
            $r = $this->actingAs($admin)->get("/admin/certificados?estado={$estado}")->assertStatus(200);
            foreach ($deben as $c) { $r->assertSee($c->codigo_unico); }
            foreach ($nodeben as $c) { $r->assertDontSee($c->codigo_unico); }
        }
    }

    public function test_el_listado_muestra_los_botones_de_filtro_de_estado(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/certificados')
            ->assertStatus(200)
            ->assertSee('Vencen en 30 días')
            ->assertSee('Vencidos')
            ->assertSee('Inactivos');
    }
}

