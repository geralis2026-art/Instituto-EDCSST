<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\ConfiguracionSitio;

/**
 * Páginas generales del sitio público (inicio y "Sobre nosotros").
 */
class HomeController extends Controller
{
    /**
     * Página de inicio del sitio público.
     */
    public function nosotros()
    {
        return view('public.nosotros');
    }

    /** Página de inicio con hasta 4 cursos destacados y configuración del sitio. */
    public function index()
    {
        // Cursos destacados para mostrar en home (máximo 4)
        $cursosDestacados = Curso::destacados()
            ->with('categoria')
            ->take(4)
            ->get();

        // Configuración del sitio
        $config = ConfiguracionSitio::obtener();

        return view('public.home', compact('cursosDestacados', 'config'));
    }
}
