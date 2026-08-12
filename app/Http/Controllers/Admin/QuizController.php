<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

/**
 * Configuración del quiz de un curso (nota mínima, intentos máximos).
 * Las preguntas se gestionan en QuizPreguntaController.
 */
class QuizController extends Controller
{
    public function edit(Curso $curso)
    {
        $quiz = $curso->quiz()->with('preguntas.opciones')->first();

        return view('admin.quiz.edit', compact('curso', 'quiz'));
    }

    public function update(Request $request, Curso $curso)
    {
        $datos = $request->validate([
            'nota_minima'      => ['required', 'numeric', 'min:0', 'max:100'],
            'intentos_maximos' => ['required', 'integer', 'min:1', 'max:10'],
            'activo'           => ['boolean'],
        ]);
        $datos['activo'] = $request->boolean('activo', true);

        $curso->quiz()->updateOrCreate(['curso_id' => $curso->id], $datos);

        return redirect()
            ->route('admin.cursos.quiz.edit', $curso)
            ->with('success', 'Configuración del quiz guardada correctamente.');
    }
}
