<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\ConfiguracionSitio;

class HomeController extends Controller
{
    /**
     * Página de inicio del sitio público.
     */
    public function nosotros()
    {
        return view('public.nosotros');
    }

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
