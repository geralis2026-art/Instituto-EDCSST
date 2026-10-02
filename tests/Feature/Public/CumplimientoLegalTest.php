<?php

namespace Tests\Feature\Public;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Habeas data (Ley 1581 de 2012): políticas publicadas, autorización registrada, aviso de cookies,
 * reCAPTCHA solo con permiso y minimización de datos.
 */
class CumplimientoLegalTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Páginas legales ----------

    public function test_las_politicas_estan_publicadas_con_el_contenido_obligatorio(): void
    {
        $this->get(route('politica.privacidad'))->assertOk()
            ->assertSee('Política de privacidad')
            ->assertSee(config('politicas.razon_social'))
            ->assertSee(config('politicas.nit'))
            ->assertSee('Ley Estatutaria 1581 de 2012')
            ->assertSee('Superintendencia de Industria y Comercio')
            ->assertSee('10 días hábiles')
            ->assertSee('15 días hábiles')
            ->assertSee('Datos sensibles')
            ->assertSee('Revocar la autorización');

        $this->get(route('terminos'))->assertOk()
            ->assertSee('Términos y condiciones')
            ->assertSee('Ley aplicable y jurisdicción');

        $this->get(route('politica.cookies'))->assertOk()
            ->assertSee('Política de cookies')
            ->assertSee(config('session.cookie'))
            ->assertSee('XSRF-TOKEN')
            ->assertSee('Google reCAPTCHA');
    }

    public function test_las_politicas_se_pueden_indexar_y_enlazan_entre_si(): void
    {
        foreach (['politica.privacidad', 'terminos', 'politica.cookies'] as $ruta) {
            $this->get(route($ruta))->assertOk()
                ->assertDontSee('noindex')
                ->assertSee(route('politica.privacidad'), false)
                ->assertSee(route('terminos'), false)
                ->assertSee(route('politica.cookies'), false);
        }
    }

    public function test_el_pie_enlaza_las_politicas_y_ofrece_cambiar_las_cookies(): void
    {
        $this->get('/')->assertOk()
            ->assertSee(route('politica.privacidad'), false)
            ->assertSee(route('terminos'), false)
            ->assertSee(route('politica.cookies'), false)
            ->assertSee('Preferencias de cookies');
    }

    // ---------- Aviso de cookies ----------

    public function test_el_aviso_de_cookies_ofrece_las_dos_opciones_con_el_mismo_peso(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('id="aviso-cookies"', false)
            ->assertSee('Solo las necesarias')
            ->assertSee('Aceptar todas')
            ->assertSee('No usamos cookies de publicidad ni de seguimiento');
    }

    public function test_recaptcha_de_google_no_se_carga_hasta_que_la_persona_lo_permite(): void
    {
        $this->get('/contacto')->assertOk()
            ->assertDontSee('<script src="https://www.google.com/recaptcha', false)
            ->assertDontSee('class="g-recaptcha"', false)
            ->assertSee('Activar verificación');
    }

    // ---------- Formulario de contacto ----------

    public function test_el_contacto_exige_la_autorizacion_de_datos(): void
    {
        Http::fake();

        $this->post('/contacto', [
            'nombre' => 'Laura Gómez', 'correo' => 'laura@ejemplo.test',
            'mensaje' => 'Quisiera información sobre los cursos.', 'g-recaptcha-response' => 'token',
        ])->assertSessionHasErrors('acepta_politica');

        $this->assertDatabaseCount('mensajes', 0);
    }

    public function test_el_contacto_guarda_la_prueba_de_la_autorizacion(): void
    {
        Http::fake(['https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true])]);

        $this->post('/contacto', [
            'nombre' => 'Laura Gómez', 'correo' => 'laura@ejemplo.test',
            'mensaje' => 'Quisiera información sobre los cursos.',
            'acepta_politica' => '1', 'g-recaptcha-response' => 'token',
        ])->assertRedirect(route('contacto'));

        $this->assertDatabaseHas('mensajes', [
            'correo' => 'laura@ejemplo.test',
            'autorizacion_datos_version' => config('politicas.version'),
        ]);
        $this->assertNotNull(\App\Models\Mensaje::first()->autorizacion_datos_at);
    }

    public function test_la_casilla_de_autorizacion_nunca_viene_premarcada(): void
    {
        $this->get('/contacto')->assertOk()
            ->assertSee('name="acepta_politica"', false)
            ->assertDontSee('name="acepta_politica" value="1" required checked', false);
    }

    // ---------- Registro público ----------

    private function tokenDeRegistro(): string
    {
        $token = 'token-legal-'.uniqid();
        Cache::put("reg:{$token}", User::factory()->admin()->create()->id, now()->addMinutes(20));

        return $token;
    }

    public function test_el_registro_exige_la_autorizacion_y_no_guarda_nada_sin_ella(): void
    {
        $curso = Curso::factory()->create(['activo' => true]);

        $this->post('/registro/'.$this->tokenDeRegistro(), [
            'nombre_completo' => 'Ana Pérez', 'tipo_documento' => 'CC', 'documento' => '900123456',
            'cursos' => [$curso->id], 'modalidades' => [$curso->id => 'virtual'],
        ])->assertSessionHasErrors('acepta_politica');

        $this->assertDatabaseMissing('capacitados', ['documento' => '900123456']);
    }

    public function test_el_registro_guarda_la_prueba_de_la_autorizacion(): void
    {
        $curso = Curso::factory()->create(['activo' => true]);

        $this->post('/registro/'.$this->tokenDeRegistro(), [
            'nombre_completo' => 'Ana Pérez', 'tipo_documento' => 'CC', 'documento' => '900123456',
            'cursos' => [$curso->id], 'modalidades' => [$curso->id => 'virtual'], 'acepta_politica' => '1',
        ])->assertOk();

        $capacitado = Capacitado::withoutGlobalScopes()->where('documento', '900123456')->firstOrFail();
        $this->assertNotNull($capacitado->autorizacion_datos_at);
        $this->assertSame(config('politicas.version'), $capacitado->autorizacion_datos_version);
    }

    public function test_el_registro_publico_ya_no_pide_el_grupo_sanguineo(): void
    {
        $this->get('/registro/'.$this->tokenDeRegistro())->assertOk()
            ->assertDontSee('Grupo sanguíneo')
            ->assertDontSee('name="rh"', false)
            ->assertSee('Política de Tratamiento de Datos Personales');
    }

    public function test_registrarse_de_nuevo_no_borra_el_rh_que_ya_estaba_guardado(): void
    {
        $curso = Curso::factory()->create(['activo' => true]);
        Capacitado::factory()->create(['documento' => '900123456', 'rh' => 'O+']);

        $this->post('/registro/'.$this->tokenDeRegistro(), [
            'nombre_completo' => 'Ana Pérez', 'tipo_documento' => 'CC', 'documento' => '900123456',
            'cursos' => [$curso->id], 'modalidades' => [$curso->id => 'virtual'], 'acepta_politica' => '1',
        ])->assertOk();

        $this->assertSame('O+', Capacitado::withoutGlobalScopes()->where('documento', '900123456')->value('rh'));
    }

    // ---------- Verificación pública: minimización ----------

    public function test_la_verificacion_publica_no_muestra_el_documento_completo(): void
    {
        $capacitado = Capacitado::factory()->create(['nombre_completo' => 'Mario Quintero', 'documento' => '1116548453']);
        Certificado::factory()->create(['capacitado_id' => $capacitado->id, 'codigo_unico' => 'EDCSST-2026-00077']);

        $this->post('/verificar', ['codigo' => 'EDCSST-2026-00077'])->assertOk()
            ->assertSee('Mario Quintero')
            ->assertSee('•••••••453')
            ->assertDontSee('1116548453');
    }

    public function test_el_documento_enmascarado_deja_solo_los_ultimos_tres_caracteres(): void
    {
        $this->assertSame('•••••••453', (new Capacitado(['documento' => '1116548453']))->documentoEnmascarado());
        $this->assertSame('•234', (new Capacitado(['documento' => '1234']))->documentoEnmascarado());
        $this->assertSame('12', (new Capacitado(['documento' => '12']))->documentoEnmascarado());
    }
}
