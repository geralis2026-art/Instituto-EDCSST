<?php

namespace Tests\Feature\Admin;

use App\Models\Capacitado;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\SolicitudCertificado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Filtros por estado (chips) de los listados de capacitados y categorías. */
class FiltrosEstadoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_capacitados_filtra_por_estado_de_certificacion(): void
    {
        $admin = $this->admin();

        $conCert   = Capacitado::factory()->create(['nombre_completo' => 'Con Certificado Vigente']);
        $sinCert   = Capacitado::factory()->create(['nombre_completo' => 'Sin Ningun Certificado']);
        $porVencer = Capacitado::factory()->create(['nombre_completo' => 'Por Vencer Pronto']);
        $vencido   = Capacitado::factory()->create(['nombre_completo' => 'Con Certificado Vencido']);
        $pendiente = Capacitado::factory()->create(['nombre_completo' => 'Con Solicitud Pendiente']);

        Certificado::factory()->create(['capacitado_id' => $conCert->id, 'fecha_vencimiento' => today()->addDays(200)->toDateString()]);
        Certificado::factory()->create(['capacitado_id' => $porVencer->id, 'fecha_vencimiento' => today()->addDays(9)->toDateString()]);
        Certificado::factory()->vencido()->create(['capacitado_id' => $vencido->id]);
        SolicitudCertificado::create([
            'capacitado_id' => $pendiente->id,
            'curso_id'      => Curso::factory()->create()->id,
            'modalidad'     => 'presencial',
            'estado'        => SolicitudCertificado::ESTADO_PENDIENTE,
            'origen'        => 'registro_link',
        ]);

        $casos = [
            'con_certificados' => [['Con Certificado Vigente', 'Por Vencer Pronto', 'Con Certificado Vencido'], ['Sin Ningun Certificado', 'Con Solicitud Pendiente']],
            'sin_certificados' => [['Sin Ningun Certificado', 'Con Solicitud Pendiente'], ['Con Certificado Vigente', 'Por Vencer Pronto', 'Con Certificado Vencido']],
            'por_vencer'       => [['Por Vencer Pronto'], ['Con Certificado Vigente', 'Con Certificado Vencido', 'Sin Ningun Certificado']],
            'vencidos'         => [['Con Certificado Vencido'], ['Con Certificado Vigente', 'Por Vencer Pronto']],
            'pendientes'       => [['Con Solicitud Pendiente'], ['Con Certificado Vigente', 'Sin Ningun Certificado']],
            'nuevos'           => [['Con Certificado Vigente', 'Sin Ningun Certificado'], []],
        ];

        foreach ($casos as $estado => [$deben, $nodeben]) {
            $r = $this->actingAs($admin)->get("/admin/capacitados?estado={$estado}")->assertStatus(200);
            foreach ($deben as $nombre) { $r->assertSee($nombre); }
            foreach ($nodeben as $nombre) { $r->assertDontSee($nombre); }
        }
    }

    public function test_el_filtro_de_capacitados_se_combina_con_la_busqueda_sin_perder_el_and(): void
    {
        $admin = $this->admin();

        $coincide = Capacitado::factory()->create(['nombre_completo' => 'Marta Ruiz', 'correo' => 'marta@ejemplo.test']);
        $otraBusqueda = Capacitado::factory()->create(['nombre_completo' => 'Pedro Gómez', 'correo' => 'pedro@ejemplo.test']);
        Certificado::factory()->create(['capacitado_id' => $coincide->id]);
        Certificado::factory()->create(['capacitado_id' => $otraBusqueda->id]);
        $sinCert = Capacitado::factory()->create(['nombre_completo' => 'Marta Sinpapeles', 'correo' => 'sin@ejemplo.test']);

        // "Marta" + "con certificados": solo Marta Ruiz (Marta Sinpapeles no tiene certificados;
        // Pedro tiene certificado pero no coincide con la búsqueda).
        $this->actingAs($admin)->get('/admin/capacitados?busqueda=Marta&estado=con_certificados')
            ->assertStatus(200)
            ->assertSee('Marta Ruiz')
            ->assertDontSee('Marta Sinpapeles')
            ->assertDontSee('Pedro Gómez');
    }

    public function test_capacitados_muestra_los_chips_de_filtro(): void
    {
        $this->actingAs($this->admin())->get('/admin/capacitados')
            ->assertStatus(200)
            ->assertSee('Sin certificados')
            ->assertSee('Por vencer (30 días)')
            ->assertSee('Solicitud pendiente');
    }

    public function test_categorias_filtra_por_estado_y_por_cursos(): void
    {
        $admin = $this->admin();

        $activaConCursos = Categoria::factory()->create(['nombre' => 'Activa Con Cursos', 'activo' => true]);
        Curso::factory()->create(['categoria_id' => $activaConCursos->id]);
        Categoria::factory()->create(['nombre' => 'Activa Vacia', 'activo' => true]);
        Categoria::factory()->create(['nombre' => 'Inactiva Vacia', 'activo' => false]);

        $casos = [
            'activas'    => [['Activa Con Cursos', 'Activa Vacia'], ['Inactiva Vacia']],
            'inactivas'  => [['Inactiva Vacia'], ['Activa Con Cursos', 'Activa Vacia']],
            'con_cursos' => [['Activa Con Cursos'], ['Activa Vacia', 'Inactiva Vacia']],
            'sin_cursos' => [['Activa Vacia', 'Inactiva Vacia'], ['Activa Con Cursos']],
        ];

        foreach ($casos as $estado => [$deben, $nodeben]) {
            $r = $this->actingAs($admin)->get("/admin/categorias?estado={$estado}")->assertStatus(200);
            foreach ($deben as $nombre) { $r->assertSee($nombre); }
            foreach ($nodeben as $nombre) { $r->assertDontSee($nombre); }
        }
    }

    public function test_un_estado_invalido_se_ignora(): void
    {
        $admin = $this->admin();
        Capacitado::factory()->create(['nombre_completo' => 'Persona Visible']);

        $this->actingAs($admin)->get('/admin/capacitados?estado=inventado')
            ->assertStatus(200)
            ->assertSee('Persona Visible');
    }

    public function test_mensajes_filtra_por_estado_con_chips(): void
    {
        $admin = $this->admin();

        \App\Models\Mensaje::factory()->create(['nombre' => 'Remitente Nuevo']);
        \App\Models\Mensaje::factory()->leido()->create(['nombre' => 'Remitente Leido']);
        \App\Models\Mensaje::factory()->respondido()->create(['nombre' => 'Remitente Respondido']);

        $r = $this->actingAs($admin)->get('/admin/mensajes?estado=nuevo')->assertStatus(200);
        $r->assertSee('Remitente Nuevo')->assertDontSee('Remitente Leido')->assertDontSee('Remitente Respondido');

        $this->actingAs($admin)->get('/admin/mensajes?estado=respondido')->assertStatus(200)
            ->assertSee('Remitente Respondido')->assertDontSee('Remitente Nuevo');

        $this->actingAs($admin)->get('/admin/mensajes')->assertSee('Respondidos')->assertSee('Leídos');
    }

    public function test_cursos_filtra_por_estado(): void
    {
        $admin = $this->admin();

        $conCert = Curso::factory()->create(['nombre' => 'Curso Con Certificados', 'activo' => true]);
        Certificado::factory()->create(['curso_id' => $conCert->id]);
        Curso::factory()->create(['nombre' => 'Curso Vacio Activo', 'activo' => true]);
        Curso::factory()->create(['nombre' => 'Curso Inactivo', 'activo' => false]);
        Curso::factory()->create(['nombre' => 'Curso Estrella', 'activo' => true, 'destacado' => true]);

        $casos = [
            'inactivos'        => [['Curso Inactivo'], ['Curso Con Certificados', 'Curso Estrella']],
            'destacados'       => [['Curso Estrella'], ['Curso Con Certificados', 'Curso Inactivo']],
            'sin_certificados' => [['Curso Vacio Activo', 'Curso Inactivo', 'Curso Estrella'], ['Curso Con Certificados']],
        ];

        foreach ($casos as $estado => [$deben, $nodeben]) {
            $r = $this->actingAs($admin)->get("/admin/cursos?estado={$estado}")->assertStatus(200);
            foreach ($deben as $n) { $r->assertSee($n); }
            foreach ($nodeben as $n) { $r->assertDontSee($n); }
        }
    }

    public function test_usuarios_filtra_por_rol_y_por_activo(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Principal']);
        User::factory()->instructor()->create(['name' => 'Instructor Uno', 'activo' => true]);
        User::factory()->capacitador()->create(['name' => 'Capacitador Uno', 'activo' => false]);

        $this->actingAs($admin)->get('/admin/usuarios?estado=instructor')->assertStatus(200)
            ->assertSee('Instructor Uno')->assertDontSee('Capacitador Uno');

        $this->actingAs($admin)->get('/admin/usuarios?estado=inactivos')->assertStatus(200)
            ->assertSee('Capacitador Uno')->assertDontSee('Instructor Uno');

        $this->actingAs($admin)->get('/admin/usuarios')->assertSee('Administradores')->assertSee('Instructores');
    }

    public function test_las_fichas_de_detalle_cargan(): void
    {
        $admin = $this->admin();
        $certificado = Certificado::factory()->create();
        $cap = $certificado->capacitado;
        $curso = $certificado->curso;
        $cat = $curso->categoria;
        $mensaje = \App\Models\Mensaje::factory()->create();

        $this->actingAs($admin)->get("/admin/capacitados/{$cap->id}")->assertStatus(200)->assertSee($certificado->codigo_unico);
        $this->actingAs($admin)->get("/admin/certificados/{$certificado->id}")->assertStatus(200)->assertSee($cap->nombre_completo);
        $this->actingAs($admin)->get("/admin/cursos/{$curso->id}")->assertStatus(200)->assertSee($curso->nombre);
        $this->actingAs($admin)->get("/admin/categorias/{$cat->id}")->assertStatus(200)->assertSee($cat->nombre);
        $this->actingAs($admin)->get("/admin/mensajes/{$mensaje->id}")->assertStatus(200)->assertSee('Gestión interna');
    }
}

