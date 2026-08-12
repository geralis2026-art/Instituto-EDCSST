<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD de módulos del aula virtual de un curso. Acceso exclusivo admin.
 */
class ModuloController extends Controller
{
    public function create(Curso $curso)
    {
        return view('admin.modulos.create', compact('curso'));
    }

    public function store(Request $request, Curso $curso)
    {
        $datos = $this->validarDatos($request);
        $datos['orden'] = $datos['orden'] ?? ($curso->modulos()->max('orden') + 1);

        $curso->modulos()->create($datos);

        return redirect()
            ->route('admin.cursos.show', $curso)
            ->with('success', 'Módulo creado correctamente.');
    }

    public function edit(Curso $curso, Modulo $modulo)
    {
        abort_unless($modulo->curso_id === $curso->id, 404);

        $modulo->load('materiales');

        return view('admin.modulos.edit', compact('curso', 'modulo'));
    }

    public function update(Request $request, Curso $curso, Modulo $modulo)
    {
        abort_unless($modulo->curso_id === $curso->id, 404);

        $modulo->update($this->validarDatos($request));

        return redirect()
            ->route('admin.cursos.modulos.edit', [$curso, $modulo])
            ->with('success', 'Módulo actualizado correctamente.');
    }

    /** Elimina el módulo. Borra primero los archivos físicos de sus materiales (la fila en BD cascadea sola). */
    public function destroy(Curso $curso, Modulo $modulo)
    {
        abort_unless($modulo->curso_id === $curso->id, 404);

        foreach ($modulo->materiales as $material) {
            if ($material->archivo) {
                Storage::disk('materiales')->delete($material->archivo);
            }
        }

        $modulo->delete();

        return redirect()
            ->route('admin.cursos.show', $curso)
            ->with('success', 'Módulo eliminado correctamente.');
    }

    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'titulo'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'orden'       => ['nullable', 'integer', 'min:0'],
            'activo'      => ['boolean'],
        ]) + ['activo' => $request->boolean('activo', true)];
    }
}
