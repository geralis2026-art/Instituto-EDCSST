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
use App\Http\Controllers\ProfileController;

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

// ── Rutas exclusivas para administradores ───────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'activo', 'admin', 'throttle:admin-general'])->group(function () {

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
    Route::get('certificados-masivos',  [CertificadoController::class, 'masivosForm'])->name('certificados.masivos');
    Route::post('certificados-masivos', [CertificadoController::class, 'generarMasivos'])->name('certificados.generar-masivos')->middleware('throttle:admin-escritura');

    // Cursos y categorías
    Route::resource('cursos',     CursoController::class);
    Route::resource('categorias', CategoriaController::class);

    // Bandeja de mensajes
    Route::resource('mensajes', MensajeController::class)->only(['index', 'show', 'update', 'destroy']);

    // Gestión de usuarios
    Route::resource('usuarios', UsuarioController::class)->only(['index', 'create']);
    Route::post('usuarios',              [UsuarioController::class, 'store'])->name('usuarios.store')->middleware('throttle:admin-escritura');
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

require __DIR__.'/auth.php';

