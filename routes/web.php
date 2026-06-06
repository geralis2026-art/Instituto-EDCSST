<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogoController;
use App\Http\Controllers\Public\ContactoController;
use App\Http\Controllers\Public\ConsultaCertificadoController;
use App\Http\Controllers\Public\VerificacionController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CapacitadoController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\CertificadoController;
use App\Http\Controllers\Admin\MensajeController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (sitio del instituto)
|--------------------------------------------------------------------------
| Cualquier visitante puede acceder a estas URLs sin necesidad de login.
*/

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Catálogo de cursos
Route::get('/cursos', [CatalogoController::class, 'index'])->name('catalogo');

// Formulario de contacto
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto');
Route::post('/contacto', [ContactoController::class, 'enviar'])->name('contacto.enviar')->middleware('throttle:contacto-publica');

// Consulta pública de certificados (capacitado busca sus certificados)
Route::get('/consulta', [ConsultaCertificadoController::class, 'index'])->name('consulta');
Route::post('/consulta', [ConsultaCertificadoController::class, 'buscar'])->name('consulta.buscar')->middleware('throttle:consulta-publica');
Route::get('/consulta/descargar/{certificado}', [ConsultaCertificadoController::class, 'descargar'])->name('consulta.descargar')->middleware('signed');

// Verificación pública (terceros verifican autenticidad)
Route::get('/verificar', [VerificacionController::class, 'index'])->name('verificar');
Route::post('/verificar', [VerificacionController::class, 'verificar'])->name('verificar.verificar')->middleware('throttle:verificacion-publica');


/*
|--------------------------------------------------------------------------
| RUTAS ADMINISTRATIVAS (panel de empleados)
|--------------------------------------------------------------------------
| Estas rutas requieren autenticación (login).
| El prefijo /admin y el middleware 'auth' las protegen.
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal del panel
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de capacitados (CRUD completo)
    Route::resource('capacitados', CapacitadoController::class);
    Route::get('capacitados-plantilla', [CapacitadoController::class, 'descargarPlantilla'])->name('capacitados.descargarPlantilla');
    Route::post('capacitados-importar', [CapacitadoController::class, 'importar'])->name('capacitados.importar');

    // Gestión de cursos
    Route::resource('cursos', CursoController::class);

    // Gestión de categorías
    Route::resource('categorias', CategoriaController::class);

    // Gestión de certificados
    Route::patch('certificados/{certificado}/toggle-activo', [CertificadoController::class, 'toggleActivo'])->name('certificados.toggle-activo');
    Route::get('certificados/{certificado}/pdf', [CertificadoController::class, 'verPdf'])->name('certificados.pdf');
    Route::resource('certificados', CertificadoController::class);
    Route::get('certificados-masivos', [CertificadoController::class, 'masivosForm'])->name('certificados.masivos');
    Route::post('certificados-masivos', [CertificadoController::class, 'generarMasivos'])->name('certificados.generar-masivos');

    // Bandeja de mensajes
    Route::resource('mensajes', MensajeController::class)->only(['index', 'show', 'update', 'destroy']);

    // Gestión de usuarios admin
    Route::resource('usuarios', UsuarioController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::patch('usuarios/{usuario}/toggle-activo', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
});

Route::redirect('/dashboard', '/admin')
    ->middleware(['auth', 'verified'])
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

