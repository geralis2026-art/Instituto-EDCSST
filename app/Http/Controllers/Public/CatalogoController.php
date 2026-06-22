<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

/**
 * Catálogo público de cursos, organizado por categoría y con
 * filtro opcional por categoría (vía slug en la URL).
 */
class CatalogoController extends Controller
{
    /** Listado del catálogo público de cursos, agrupado por categoría. */
    public function index(Request $request)
    {
        $categorias = Categoria::activas()
            ->whereHas('cursos', fn ($q) => $q->where('activo', true))
            ->with(['cursos' => fn ($q) => $q->where('activo', true)->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        $categoriaSeleccionada = null;

        if ($request->filled('categoria')) {
            $categoriaSeleccionada = Categoria::where('slug', $request->query('categoria'))->first();
        }

        return view('public.catalogo', compact('categorias', 'categoriaSeleccionada'));
    }
}
