<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Material;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Alta/baja de material de estudio dentro de un módulo. Se sube al disco
 * privado "materiales" (documentos/talleres/presentaciones) o se guarda
 * como enlace externo (típicamente videos).
 */
class MaterialController extends Controller
{
    public function store(Request $request, Curso $curso, Modulo $modulo)
    {
        abort_unless($modulo->curso_id === $curso->id, 404);

        $datos = $request->validate([
            'titulo'  => ['required', 'string', 'max:255'],
            'tipo'    => ['required', 'in:' . implode(',', array_keys(Material::TIPOS))],
            'archivo' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,mp4,mov,jpg,jpeg,png', 'max:20480'],
            'url'     => ['nullable', 'url', 'max:2000'],
        ]);

        if (!$request->hasFile('archivo') && empty($datos['url'])) {
            return back()->withInput()->with('error', 'Debes subir un archivo o indicar un enlace (URL).');
        }

        if ($request->hasFile('archivo')) {
            $datos['archivo'] = $request->file('archivo')->store('modulo-' . $modulo->id, 'materiales');
        }

        $datos['orden'] = $modulo->materiales()->max('orden') + 1;

        $modulo->materiales()->create($datos);

        return redirect()
            ->route('admin.cursos.modulos.edit', [$curso, $modulo])
            ->with('success', 'Material agregado correctamente.');
    }

    public function destroy(Curso $curso, Material $material)
    {
        $modulo = $material->modulo;

        abort_unless($modulo->curso_id === $curso->id, 404);

        if ($material->archivo) {
            Storage::disk('materiales')->delete($material->archivo);
        }

        $material->delete();

        return redirect()
            ->route('admin.cursos.modulos.edit', [$curso, $modulo])
            ->with('success', 'Material eliminado correctamente.');
    }
}
