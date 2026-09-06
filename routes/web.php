<?php

use App\Http\Controllers\UploadsController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogoController;
use App\Http\Controllers\Public\ContactoController;
use App\Http\Controllers\Public\ConsultaCertificadoController;
use App\Http\Controllers\Public\VerificacionController;
use App\Http\Controllers\Public\RegistroCapacitadoController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CapacitadoController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\CertificadoController;
use App\Http\Controllers\Admin\MensajeController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuizPreguntaController;
use App\Http\Controllers\Admin\MatriculaController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Aula\PasswordCambioController;
use App\Http\Controllers\Aula\DashboardController as AulaDashboardController;
use App\Http\Controllers\Aula\CursoController as AulaCursoController;
use App\Http\Controllers\Aula\QuizController as AulaQuizController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (sitio del instituto)
|--------------------------------------------------------------------------
| Cualquier visitante puede acceder a estas URLs sin necesidad de login.
*/

// Servir archivos subidos (imágenes de cursos, logos) desde storage persistente
Route::get('/uploads/{type}/{filename}', [UploadsController::class, 'serve'])
    ->where('type', 'cursos|logos')
    ->where('filename', '[^/]+')
    ->name('uploads.serve');

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Sobre nosotros
Route::get('/nosotros', [HomeController::class, 'nosotros'])->name('nosotros');

// Catálogo de cursos
Route::get('/cursos', [CatalogoController::class, 'index'])->name('catalogo');

// Formulario de contacto
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto');
Route::post('/contacto', [ContactoController::class, 'enviar'])->name('contacto.enviar')->middleware('throttle:contacto-publica');

// Consulta pública de certificados (capacitado busca sus certificados)
Route::get('/consulta', [ConsultaCertificadoController::class, 'index'])->name('consulta');
Route::post('/consulta', [ConsultaCertificadoController::class, 'buscar'])->name('consulta.buscar')->middleware('throttle:consulta-publica');
Route::get('/consulta/descargar/{certificado}', [ConsultaCertificadoController::class, 'descargar'])->name('consulta.descargar')->middleware('signed');
Route::get('/consulta/descargar-todos/{capacitado}', [ConsultaCertificadoController::class, 'descargarTodos'])->name('consulta.descargarTodos')->middleware('signed');
Route::post('/consulta/descargar-seleccionados/{capacitado}', [ConsultaCertificadoController::class, 'descargarSeleccionados'])->name('consulta.descargarSeleccionados')->middleware('signed');

// Verificación pública (terceros verifican autenticidad)
Route::get('/verificar', [VerificacionController::class, 'index'])->name('verificar');
Route::post('/verificar', [VerificacionController::class, 'verificar'])->name('verificar.verificar')->middleware('throttle:verificacion-publica');

// Auto-registro de capacitados (link temporal generado desde el admin)
Route::get('/registro/{token}', [RegistroCapacitadoController::class, 'form'])->name('registro.form');
Route::post('/registro/{token}', [RegistroCapacitadoController::class, 'guardar'])->name('registro.guardar')->middleware('throttle:registro-publica');

/*
|--------------------------------------------------------------------------
| RUTAS ADMINISTRATIVAS (panel de empleados)
|--------------------------------------------------------------------------
| Estas rutas requieren autenticación (login).
| El prefijo /admin y el middleware 'auth' las protegen.
*/

// ── Rutas accesibles para todos los roles autenticados ──────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'activo', 'throttle:admin-general'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Capacitados — solo lectura
    Route::get('capacitados',              [CapacitadoController::class, 'index'])->name('capacitados.index');
    Route::get('capacitados/buscar',       [CapacitadoController::class, 'buscar'])->name('capacitados.buscar');
    Route::get('capacitados/{capacitado}', [CapacitadoController::class, 'show'])->name('capacitados.show')->whereNumber('capacitado');
    Route::get('capacitados/{capacitado}/certificados-pdf', [CapacitadoController::class, 'descargarCertificados'])->name('capacitados.descargarCertificados')->whereNumber('capacitado');

    // Certificados — ver, crear y descargar PDF
    Route::get('certificados/{certificado}/pdf', [CertificadoController::class, 'verPdf'])->name('certificados.pdf')->whereNumber('certificado');
    Route::get('certificados',           [CertificadoController::class, 'index'])->name('certificados.index');
    Route::get('certificados/create',    [CertificadoController::class, 'create'])->name('certificados.create');
    Route::get('certificados/{certificado}', [CertificadoController::class, 'show'])->name('certificados.show')->whereNumber('certificado');
    Route::post('certificados',          [CertificadoController::class, 'store'])->name('certificados.store')->middleware('throttle:admin-escritura');
});

// ── Cursos — solo lectura para admin y capacitador (el instructor no tiene acceso a cursos) ──
Route::prefix('admin')->name('admin.')->middleware(['auth', 'activo', 'cursos.ver', 'throttle:admin-general'])->group(function () {

    // whereNumber es obligatorio: sin él, "cursos/create" (ruta solo-admin, registrada más abajo)
    // sería interceptada por este {curso} wildcard y devolvería 404 en vez de 403.
    Route::get('cursos',           [CursoController::class, 'index'])->name('cursos.index');
    Route::get('cursos/{curso}',   [CursoController::class, 'show'])->name('cursos.show')->whereNumber('curso');

    // Matrículas — solo lectura, para validar quién completó el curso
    // (aula virtual, ver config/features.php: módulo aún no pagado)
    Route::get('cursos/{curso}/matriculas', [MatriculaController::class, 'index'])->name('cursos.matriculas.index')->whereNumber('curso')->middleware('feature:aula_virtual');
});

// ── Rutas de gestión: admin + instructor (CRUD de lo propio, ver PropietarioScope) ──
Route::prefix('admin')->name('admin.')->middleware(['auth', 'activo', 'gestor', 'throttle:admin-general'])->group(function () {

    // Capacitados — CRUD completo (escritura con límite más estricto)
    Route::get('capacitados/link-registro',   [CapacitadoController::class, 'generarLinkRegistro'])->name('capacitados.link-registro');
    Route::get('capacitados/create',          [CapacitadoController::class, 'create'])->name('capacitados.create');
    Route::post('capacitados',                [CapacitadoController::class, 'store'])->name('capacitados.store')->middleware('throttle:admin-escritura');
    Route::get('capacitados/{capacitado}/edit',  [CapacitadoController::class, 'edit'])->name('capacitados.edit')->whereNumber('capacitado');
    Route::put('capacitados/{capacitado}',    [CapacitadoController::class, 'update'])->name('capacitados.update')->whereNumber('capacitado')->middleware('throttle:admin-escritura');
    Route::delete('capacitados/{capacitado}', [CapacitadoController::class, 'destroy'])->name('capacitados.destroy')->whereNumber('capacitado')->middleware('throttle:admin-escritura');
    Route::get('capacitados-plantilla',       [CapacitadoController::class, 'descargarPlantilla'])->name('capacitados.descargarPlantilla');
    Route::get('capacitados-importar',        [CapacitadoController::class, 'importarForm'])->name('capacitados.importar.form');
    Route::post('capacitados-importar',       [CapacitadoController::class, 'importar'])->name('capacitados.importar')->middleware('throttle:admin-escritura');
    Route::post('capacitados-importar/confirmar', [CapacitadoController::class, 'importarConfirmar'])->name('capacitados.importar.confirmar')->middleware('throttle:admin-escritura');

    // Certificados — editar, eliminar, activar/desactivar, masivos
    Route::get('certificados/{certificado}/edit',  [CertificadoController::class, 'edit'])->name('certificados.edit')->whereNumber('certificado');
    Route::put('certificados/{certificado}',    [CertificadoController::class, 'update'])->name('certificados.update')->whereNumber('certificado')->middleware('throttle:admin-escritura');
    Route::delete('certificados/{certificado}', [CertificadoController::class, 'destroy'])->name('certificados.destroy')->whereNumber('certificado')->middleware('throttle:admin-escritura');
    Route::patch('certificados/{certificado}/toggle-activo', [CertificadoController::class, 'toggleActivo'])->name('certificados.toggle-activo')->middleware('throttle:admin-escritura');
    Route::post('certificados/{certificado}/regenerar-pdf', [CertificadoController::class, 'regenerarPdf'])->name('certificados.regenerar-pdf')->whereNumber('certificado')->middleware('throttle:admin-escritura');
    Route::post('certificados/{certificado}/reenviar-correo', [CertificadoController::class, 'reenviarCorreo'])->name('certificados.reenviar-correo')->whereNumber('certificado')->middleware('throttle:admin-escritura');
    Route::get('certificados-masivos',  [CertificadoController::class, 'masivosForm'])->name('certificados.masivos');
    Route::post('certificados-masivos', [CertificadoController::class, 'generarMasivos'])->name('certificados.generar-masivos')->middleware('throttle:admin-escritura');

});

// ── Rutas exclusivas para administradores (Edna) ─────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'activo', 'admin', 'throttle:admin-general'])->group(function () {

    // Cursos — escritura (index/show son de lectura, ver grupo admin+capacitador arriba)
    Route::resource('cursos', CursoController::class)->except(['index', 'show']);

    // Aula virtual: módulos, materiales, quiz y matrículas de un curso
    // (módulo Fase 3 aún no pagado — ver config/features.php)
    Route::prefix('cursos/{curso}')->name('cursos.')->middleware('feature:aula_virtual')->group(function () {
        Route::get('modulos/create',        [ModuloController::class, 'create'])->name('modulos.create');
        Route::post('modulos',              [ModuloController::class, 'store'])->name('modulos.store')->middleware('throttle:admin-escritura');
        Route::get('modulos/{modulo}/edit', [ModuloController::class, 'edit'])->name('modulos.edit');
        Route::put('modulos/{modulo}',      [ModuloController::class, 'update'])->name('modulos.update')->middleware('throttle:admin-escritura');
        Route::delete('modulos/{modulo}',   [ModuloController::class, 'destroy'])->name('modulos.destroy')->middleware('throttle:admin-escritura');

        Route::post('modulos/{modulo}/materiales',  [MaterialController::class, 'store'])->name('materiales.store')->middleware('throttle:admin-escritura');
        Route::delete('materiales/{material}',      [MaterialController::class, 'destroy'])->name('materiales.destroy')->middleware('throttle:admin-escritura');

        Route::get('quiz', [QuizController::class, 'edit'])->name('quiz.edit');
        Route::put('quiz', [QuizController::class, 'update'])->name('quiz.update')->middleware('throttle:admin-escritura');

        Route::get('quiz/preguntas/create',        [QuizPreguntaController::class, 'create'])->name('quiz.preguntas.create');
        Route::post('quiz/preguntas',               [QuizPreguntaController::class, 'store'])->name('quiz.preguntas.store')->middleware('throttle:admin-escritura');
        Route::get('quiz/preguntas/{pregunta}/edit', [QuizPreguntaController::class, 'edit'])->name('quiz.preguntas.edit');
        Route::put('quiz/preguntas/{pregunta}',      [QuizPreguntaController::class, 'update'])->name('quiz.preguntas.update')->middleware('throttle:admin-escritura');
        Route::delete('quiz/preguntas/{pregunta}',   [QuizPreguntaController::class, 'destroy'])->name('quiz.preguntas.destroy')->middleware('throttle:admin-escritura');

        // 'matriculas.index' (lectura) está en el grupo admin+capacitador arriba, no aquí.
        Route::post('matriculas',             [MatriculaController::class, 'store'])->name('matriculas.store')->middleware('throttle:admin-escritura');
        Route::delete('matriculas/{matricula}', [MatriculaController::class, 'destroy'])->name('matriculas.destroy')->middleware('throttle:admin-escritura');
    });

    // Categorías de cursos
    Route::resource('categorias', CategoriaController::class);

    // Bandeja de mensajes
    Route::resource('mensajes', MensajeController::class)->only(['index', 'show', 'update', 'destroy']);

    // Gestión de usuarios
    Route::resource('usuarios', UsuarioController::class)->only(['index', 'create']);
    Route::post('usuarios',              [UsuarioController::class, 'store'])->name('usuarios.store')->middleware('throttle:admin-escritura');
    Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{usuario}',     [UsuarioController::class, 'update'])->name('usuarios.update')->middleware('throttle:admin-escritura');
    Route::delete('usuarios/{usuario}',  [UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('throttle:admin-escritura');
    Route::patch('usuarios/{usuario}/toggle-activo', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle-activo')->middleware('throttle:admin-escritura');

    // Configuración del sitio
    Route::get('configuracion',  [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
    Route::put('configuracion',  [ConfiguracionController::class, 'update'])->name('configuracion.update')->middleware('throttle:admin-escritura');
});

Route::redirect('/dashboard', '/admin')
    ->middleware(['auth', 'activo'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN (Breeze) y PERFIL
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| AULA VIRTUAL (capacitados)
|--------------------------------------------------------------------------
| Guard "capacitados", separado del guard "web" (empleados). El login es
| el mismo formulario de /login (ver LoginRequest::authenticate()).
| Módulo de Fase 3 aún no pagado: bloqueado con 'feature:aula_virtual'
| (ver config/features.php).
*/

// Cambio obligatorio de contraseña en el primer ingreso: fuera del middleware
// de bloqueo para no generar un redirect loop.
Route::middleware(['auth:capacitados', 'throttle:aula-general', 'feature:aula_virtual'])->prefix('aula')->name('aula.')->group(function () {
    Route::get('password/cambiar', [PasswordCambioController::class, 'edit'])->name('password.cambiar');
    Route::post('password/cambiar', [PasswordCambioController::class, 'update'])->name('password.cambiar.store');
});

Route::middleware(['auth:capacitados', 'capacitado.cambiar_password', 'throttle:aula-general', 'feature:aula_virtual'])->prefix('aula')->name('aula.')->group(function () {
    Route::get('/', [AulaDashboardController::class, 'index'])->name('dashboard');

    Route::get('cursos/{matricula}', [AulaCursoController::class, 'show'])->name('cursos.show');
    Route::post('modulos/{modulo}/completar', [AulaCursoController::class, 'completarModulo'])->name('modulos.completar');
    Route::get('materiales/{material}/descargar', [AulaCursoController::class, 'descargarMaterial'])->name('materiales.descargar');

    Route::get('cursos/{matricula}/quiz', [AulaQuizController::class, 'show'])->name('quiz.show');
    Route::post('cursos/{matricula}/quiz', [AulaQuizController::class, 'store'])->name('quiz.store')->middleware('throttle:aula-quiz');
});

require __DIR__.'/auth.php';

