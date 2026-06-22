<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;

/**
 * CRUD de categorías de cursos. Acceso exclusivo para admin
 * (ver routes/web.php).
 */
class CategoriaController extends Controller
{
    /** Lista paginada de categorías con búsqueda y conteo de cursos asociados. */
    public function index(Request $request)
    {
        $busqueda = substr(trim((string) $request->query('busqueda', '')), 0, 100);

        $categorias = Categoria::withCount('cursos')
            ->when($busqueda, fn ($query) =>
                $query->where(fn ($q) =>
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%")
                )
            )
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categorias.index', compact('categorias', 'busqueda'));
    }

    /** Formulario para crear una nueva categoría. */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /** Guarda la nueva categoría. */
    public function store(CategoriaRequest $request)
    {
        $categoria = Categoria::create($request->validated());

        return redirect()
            ->route('admin.categorias.show', $categoria)
            ->with('success', 'Categoría creada correctamente.');
    }

    /** Detalle de la categoría con lista paginada de sus cursos. */
    public function show(Categoria $categoria)
    {
        $categoria->loadCount('cursos');

        $cursos = $categoria->cursos()
            ->orderBy('nombre')
            ->paginate(10);

        return view('admin.categorias.show', compact('categoria', 'cursos'));
    }

    /** Formulario para editar una categoría existente. */
    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    /** Actualiza la categoría. */
    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return redirect()
            ->route('admin.categorias.show', $categoria)
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /** Elimina la categoría. Bloqueado si tiene cursos asociados. */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->cursos()->exists()) {
            return back()
                ->with('error', 'No se puede eliminar esta categoría porque tiene cursos asociados.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
