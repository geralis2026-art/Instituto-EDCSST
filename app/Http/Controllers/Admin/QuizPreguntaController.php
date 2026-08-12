<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\QuizPregunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Preguntas y opciones del quiz de un curso. Cada pregunta tiene
 * exactamente una opción correcta (coincide con la UI de toma del quiz,
 * que usa selección única tipo radio).
 */
class QuizPreguntaController extends Controller
{
    public function create(Curso $curso)
    {
        $quiz = $curso->quiz;

        if (!$quiz) {
            return redirect()
                ->route('admin.cursos.quiz.edit', $curso)
                ->with('error', 'Primero debes configurar el quiz (nota mínima e intentos) antes de agregar preguntas.');
        }

        return view('admin.quiz.preguntas.create', compact('curso', 'quiz'));
    }

    public function store(Request $request, Curso $curso)
    {
        $quiz = $curso->quiz()->firstOrFail();
        $datos = $this->validarDatos($request);

        DB::transaction(function () use ($quiz, $datos) {
            $pregunta = $quiz->preguntas()->create([
                'enunciado' => $datos['enunciado'],
                'tipo'      => $datos['tipo'],
                'orden'     => $quiz->preguntas()->max('orden') + 1,
            ]);

            $this->guardarOpciones($pregunta, $datos);
        });

        return redirect()
            ->route('admin.cursos.quiz.edit', $curso)
            ->with('success', 'Pregunta agregada correctamente.');
    }

    public function edit(Curso $curso, QuizPregunta $pregunta)
    {
        abort_unless($pregunta->quiz->curso_id === $curso->id, 404);

        $pregunta->load('opciones');

        return view('admin.quiz.preguntas.edit', compact('curso', 'pregunta'));
    }

    public function update(Request $request, Curso $curso, QuizPregunta $pregunta)
    {
        abort_unless($pregunta->quiz->curso_id === $curso->id, 404);

        $datos = $this->validarDatos($request);

        DB::transaction(function () use ($pregunta, $datos) {
            $pregunta->update([
                'enunciado' => $datos['enunciado'],
                'tipo'      => $datos['tipo'],
            ]);

            $pregunta->opciones()->delete();
            $this->guardarOpciones($pregunta, $datos);
        });

        return redirect()
            ->route('admin.cursos.quiz.edit', $curso)
            ->with('success', 'Pregunta actualizada correctamente.');
    }

    public function destroy(Curso $curso, QuizPregunta $pregunta)
    {
        abort_unless($pregunta->quiz->curso_id === $curso->id, 404);

        $pregunta->delete();

        return redirect()
            ->route('admin.cursos.quiz.edit', $curso)
            ->with('success', 'Pregunta eliminada correctamente.');
    }

    /**
     * Las opciones vienen como array asociativo (opciones[a], opciones[b]...)
     * para que el índice de "opción correcta" no dependa de la posición —
     * así se pueden dejar campos vacíos en el formulario (ej. solo 2 de 4
     * para verdadero/falso) sin que se descuadre la respuesta marcada.
     */
    private function validarDatos(Request $request): array
    {
        $opciones = array_filter(
            (array) $request->input('opciones', []),
            fn ($texto) => trim((string) $texto) !== ''
        );

        $request->merge(['opciones' => $opciones]);

        $datos = $request->validate([
            'enunciado'       => ['required', 'string', 'max:2000'],
            'tipo'            => ['required', 'in:seleccion_multiple,verdadero_falso'],
            'opciones'        => ['required', 'array', 'min:2', 'max:6'],
            'opciones.*'      => ['required', 'string', 'max:255'],
            'opcion_correcta' => ['required', 'string'],
        ]);

        if (!array_key_exists($datos['opcion_correcta'], $datos['opciones'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'opcion_correcta' => 'Selecciona cuál opción es la correcta.',
            ]);
        }

        return $datos;
    }

    private function guardarOpciones(QuizPregunta $pregunta, array $datos): void
    {
        $orden = 1;

        foreach ($datos['opciones'] as $clave => $texto) {
            $pregunta->opciones()->create([
                'texto'       => $texto,
                'es_correcta' => $clave === $datos['opcion_correcta'],
                'orden'       => $orden++,
            ]);
        }
    }
}
