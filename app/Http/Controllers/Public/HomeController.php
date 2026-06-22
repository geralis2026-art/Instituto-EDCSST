<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\ConfiguracionSitio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

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
        $cursosDestacados = collect(
            Cache::remember('home_cursos_destacados', 600, fn () =>
                Curso::destacados()->with('categoria')->take(4)->get()->toArray()
            )
        )->map(function (array $data) {
            $curso = (new Curso)->forceFill(Arr::except($data, ['categoria']))->syncOriginal();
            $curso->setRelation(
                'categoria',
                isset($data['categoria']) ? (new Categoria)->forceFill($data['categoria'])->syncOriginal() : null
            );
            return $curso;
        });

        $config = ConfiguracionSitio::obtener();

        return view('public.home', compact('cursosDestacados', 'config'));
    }
}
