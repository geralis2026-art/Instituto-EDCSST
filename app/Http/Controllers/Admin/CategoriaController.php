<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->query('busqueda', '');

        $categorias = Categoria::withCount('cursos')
            ->when($busqueda, function ($query, $busqueda) {
                $busqueda = trim($busqueda);

                return $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.categorias.index', compact('categorias', 'busqueda'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(CategoriaRequest $request)
    {
        $categoria = Categoria::create($request->validated());

        return redirect()
            ->route('admin.categorias.show', $categoria)
            ->with('success', 'Categoria creada correctamente.');
    }

    public function show(Categoria $categoria)
    {
        $categoria->loadCount('cursos');

        $cursos = $categoria->cursos()
            ->orderBy('nombre')
            ->paginate(10);

        return view('admin.categorias.show', compact('categoria', 'cursos'));
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return redirect()
            ->route('admin.categorias.show', $categoria)
            ->with('success', 'Categoria actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->cursos()->exists()) {
            return back()
                ->with('error', 'No se puede eliminar esta categoria porque tiene cursos asociados.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoria eliminada correctamente.');
    }
}
