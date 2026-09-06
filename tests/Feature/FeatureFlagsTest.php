<?php

namespace Tests\Feature;

use App\Models\Capacitado;
use App\Models\Curso;
use App\Models\User;
use App\Notifications\CertificadoEmitido;
use App\Services\CertificadoCorreoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Aula virtual y comunicaciones son módulos de Fase 3 aún no pagados por el
 * cliente (ver config/features.php): deben quedar ocultos y bloqueados (404
 * en rutas, sin envío de correos) hasta que se activen los flags de entorno,
 * sin tocar código.
 */
class FeatureFlagsTest extends TestCase
{
    use RefreshDatabase;

    // ── Aula virtual ──────────────────────────────────────────────────────

    public function test_rutas_de_aula_virtual_bloqueadas_por_defecto(): void
    {
        $capacitado = Capacitado::factory()->create(['debe_cambiar_password' => false]);

        $this->actingAs($capacitado, 'capacitados')
            ->get('/aula')
            ->assertStatus(404);
    }

    public function test_rutas_de_aula_virtual_disponibles_si_se_activa_el_flag(): void
    {
        config(['features.aula_virtual' => true]);

        $capacitado = Capacitado::factory()->create(['debe_cambiar_password' => false]);

        $this->actingAs($capacitado, 'capacitados')
            ->get('/aula')
            ->assertOk();
    }

    public function test_login_no_autentica_guard_capacitados_si_aula_virtual_esta_desactivada(): void
    {
        $capacitado = Capacitado::factory()->create([
            'correo'    => 'estudiante@example.com',
            'password'  => bcrypt('clave-secreta-123'),
        ]);

        $this->post('/login', [
            'email'    => 'estudiante@example.com',
            'password' => 'clave-secreta-123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('capacitados');
    }

    public function test_admin_no_puede_administrar_modulos_del_curso_si_aula_virtual_esta_desactivada(): void
    {
        $admin = User::factory()->admin()->create();
        $curso = Curso::factory()->create();

        $this->actingAs($admin)
            ->get("/admin/cursos/{$curso->id}/modulos/create")
            ->assertStatus(404);

        $this->actingAs($admin)
            ->get("/admin/cursos/{$curso->id}/matriculas")
            ->assertStatus(404);
    }

    // ── Comunicaciones ────────────────────────────────────────────────────

    public function test_mensaje_de_contacto_no_notifica_al_instituto_si_comunicaciones_esta_desactivado(): void
    {
        Mail::fake();
        \Illuminate\Support\Facades\Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => \Illuminate\Support\Facades\Http::response(['success' => true]),
        ]);

        $this->post('/contacto', [
            'nombre'                => 'Juan Pérez',
            'correo'                => 'juan@example.com',
            'mensaje'               => 'Hola, quiero información.',
            'g-recaptcha-response'  => 'token-de-prueba',
        ]);

        $this->assertDatabaseHas('mensajes', ['correo' => 'juan@example.com']);
        Mail::assertNothingSent();
    }

    public function test_certificado_correo_service_no_envia_si_comunicaciones_esta_desactivado(): void
    {
        Notification::fake();

        $capacitado = Capacitado::factory()->create(['correo' => 'persona@example.com']);
        $curso      = Curso::factory()->create();
        $certificado = \App\Models\Certificado::factory()->create([
            'capacitado_id' => $capacitado->id,
            'curso_id'      => $curso->id,
            'archivo_pdf'   => 'certificados/fake.pdf',
        ]);

        $enviado = app(CertificadoCorreoService::class)->enviar($certificado);

        $this->assertFalse($enviado);
        Notification::assertNothingSent();
    }

    public function test_certificado_correo_service_si_envia_cuando_comunicaciones_esta_activo(): void
    {
        config(['features.comunicaciones' => true]);
        Notification::fake();

        $capacitado = Capacitado::factory()->create(['correo' => 'persona@example.com']);
        $curso      = Curso::factory()->create();
        $certificado = \App\Models\Certificado::factory()->create([
            'capacitado_id' => $capacitado->id,
            'curso_id'      => $curso->id,
            'archivo_pdf'   => 'certificados/fake.pdf',
        ]);

        $enviado = app(CertificadoCorreoService::class)->enviar($certificado);

        $this->assertTrue($enviado);
        Notification::assertSentTo($capacitado, CertificadoEmitido::class);
    }
}
