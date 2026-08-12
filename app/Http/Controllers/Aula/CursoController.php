<?php

namespace App\Http\Controllers\Aula;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\ProgresoModulo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CursoController extends Controller
{
    /** Detalle del curso matriculado: módulos, materiales y avance del capacitado. */
    public function show(Matricula $matricula): View
    {
        $this->autorizarMatricula($matricula);

        $matricula->load([
            'curso.modulos' => fn ($q) => $q->activos(),
            'curso.modulos.materiales',
            'curso.quiz',
            'progresoModulos',
        ]);

        $completados = $matricula->progresoModulos->pluck('modulo_id')->all();

        return view('aula.curso-show', compact('matricula', 'completados'));
    }

    /** Marca un módulo como completado para la matrícula del capacitado autenticado. */
    public function completarModulo(Modulo $modulo): RedirectResponse
    {
        $capacitado = Auth::guard('capacitados')->user();

        $matricula = Matricula::where('curso_id', $modulo->curso_id)
            ->where('capacitado_id', $capacitado->id)
            ->firstOrFail();

        ProgresoModulo::firstOrCreate(
            ['matricula_id' => $matricula->id, 'modulo_id' => $modulo->id],
            ['completado_en' => now()]
        );

        return redirect()->route('aula.cursos.show', $matricula)
            ->with('success', 'Módulo marcado como completado.');
    }

    /** Descarga un material (documento/taller/presentación) verificando que el capacitado esté matriculado en su curso. */
    public function descargarMaterial(Material $material): StreamedResponse
    {
        $material->load('modulo');
        $capacitado = Auth::guard('capacitados')->user();

        $matriculado = Matricula::where('curso_id', $material->modulo->curso_id)
            ->where('capacitado_id', $capacitado->id)
            ->exists();

        abort_unless($matriculado, 403);
        abort_unless($material->archivo && Storage::disk('materiales')->exists($material->archivo), 404);

        return Storage::disk('materiales')->download($material->archivo, $material->titulo);
    }

    /** Verifica que la matrícula pertenezca al capacitado autenticado. */
    private function autorizarMatricula(Matricula $matricula): void
    {
        abort_unless(
            $matricula->capacitado_id === Auth::guard('capacitados')->id(),
            403
        );
    }
}
