<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CursoRequest;
use App\Models\Categoria;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->query('busqueda', '');
        $categoriaId = $request->query('categoria_id', '');

        $categorias = Categoria::orderBy('nombre')->get();

        $cursos = Curso::with('categoria')
            ->withCount('certificados')
            ->when($busqueda, function ($query, $busqueda) {
                $busqueda = trim($busqueda);

                return $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion_corta', 'like', "%{$busqueda}%");
                });
            })
            ->when($categoriaId, fn ($query) => $query->where('categoria_id', $categoriaId))
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.cursos.index', compact('cursos', 'categorias', 'busqueda', 'categoriaId'));
    }

    public function create()
    {
        $categorias = Categoria::activas()->orderBy('nombre')->get();

        return view('admin.cursos.create', compact('categorias'));
    }

    public function store(CursoRequest $request)
    {
        $curso = Curso::create($request->validated());

        return redirect()
            ->route('admin.cursos.show', $curso)
            ->with('success', 'Curso creado correctamente.');
    }

    public function show(Curso $curso)
    {
        $curso->load('categoria')->loadCount('certificados');

        return view('admin.cursos.show', compact('curso'));
    }

    public function edit(Curso $curso)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('admin.cursos.edit', compact('curso', 'categorias'));
    }

    public function update(CursoRequest $request, Curso $curso)
    {
        $curso->update($request->validated());

        return redirect()
            ->route('admin.cursos.show', $curso)
            ->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso)
    {
        if ($curso->certificados()->exists()) {
            return back()
                ->with('error', 'No se puede eliminar este curso porque tiene certificados asociados.');
        }

        $curso->delete();

        return redirect()
            ->route('admin.cursos.index')
            ->with('success', 'Curso eliminado correctamente.');
    }
}
