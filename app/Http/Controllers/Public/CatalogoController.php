<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    /**
     * Listado del catálogo público de cursos, agrupado por categoría.
     */
    public function index(Request $request)
    {
        // Obtener todas las categorías activas que tengan al menos un curso
        $categorias = Categoria::activas()
            ->whereHas('cursos', function ($query) {
                $query->where('activo', true);
            })
            ->with(['cursos' => function ($query) {
                $query->where('activo', true)->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        // Si filtran por categoría específica
        $categoriaSeleccionada = null;
        if ($request->filled('categoria')) {
            $categoriaSeleccionada = Categoria::where('slug', $request->categoria)->first();
        }

        return view('public.catalogo', compact('categorias', 'categoriaSeleccionada'));
    }
}
