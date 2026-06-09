<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CursoRequest;
use App\Models\Categoria;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CursoController extends Controller
{
    /** Lista paginada de cursos con búsqueda por nombre/descripción y filtro por categoría. */
    public function index(Request $request)
    {
        $busqueda    = substr(trim((string) $request->query('busqueda', '')), 0, 100);
        $categoriaId = (int) $request->query('categoria_id', 0) ?: '';

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

    /** Formulario para crear un nuevo curso. */
    public function create()
    {
        $categorias = Categoria::activas()->orderBy('nombre')->get();

        return view('admin.cursos.create', compact('categorias'));
    }

    /** Guarda el nuevo curso (el slug se genera automáticamente desde el nombre). */
    public function store(CursoRequest $request)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('cursos', 'uploads');
        } else {
            unset($datos['imagen']);
        }

        $curso = Curso::create($datos);

        return redirect()
            ->route('admin.cursos.show', $curso)
            ->with('success', 'Curso creado correctamente.');
    }

    /** Detalle del curso con su categoría y conteo de certificados emitidos. */
    public function show(Curso $curso)
    {
        $curso->load('categoria')->loadCount('certificados');

        return view('admin.cursos.show', compact('curso'));
    }

    /** Formulario para editar un curso existente. */
    public function edit(Curso $curso)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('admin.cursos.edit', compact('curso', 'categorias'));
    }

    /** Actualiza el curso. */
    public function update(CursoRequest $request, Curso $curso)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($curso->imagen) {
                Storage::disk('uploads')->delete($curso->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('cursos', 'uploads');
        } else {
            unset($datos['imagen']);
        }

        $curso->update($datos);

        return redirect()
            ->route('admin.cursos.show', $curso)
            ->with('success', 'Curso actualizado correctamente.');
    }

    /** Elimina el curso. Bloqueado si tiene certificados asociados. */
    public function destroy(Curso $curso)
    {
        if ($curso->certificados()->exists()) {
            return back()
                ->with('error', 'No se puede eliminar este curso porque tiene certificados asociados.');
        }

        if ($curso->imagen) {
            Storage::disk('uploads')->delete($curso->imagen);
        }

        $curso->delete();

        return redirect()
            ->route('admin.cursos.index')
            ->with('success', 'Curso eliminado correctamente.');
    }
}
