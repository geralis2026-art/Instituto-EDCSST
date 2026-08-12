<?php

namespace App\Http\Controllers\Aula;

use App\Http\Controllers\Controller;
use App\Models\Matricula;
use App\Models\QuizIntento;
use App\Services\CertificadoAutomaticoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class QuizController extends Controller
{
    /** Muestra las preguntas del quiz en orden aleatorio. */
    public function show(Matricula $matricula): View|RedirectResponse
    {
        $this->autorizarMatricula($matricula);

        $quiz = $matricula->curso->quiz()->with('preguntas.opciones')->firstOrFail();

        if ($matricula->completado) {
            return redirect()->route('aula.cursos.show', $matricula)
                ->with('success', 'Ya aprobaste este curso.');
        }

        if ($matricula->intentos_usados >= $quiz->intentos_maximos) {
            return redirect()->route('aula.cursos.show', $matricula)
                ->with('error', 'Ya agotaste el número de intentos permitidos.');
        }

        $preguntas = $quiz->preguntas->shuffle();

        return view('aula.quiz-show', compact('matricula', 'quiz', 'preguntas'));
    }

    /** Califica el intento; si aprueba, genera el certificado automáticamente. */
    public function store(Request $request, Matricula $matricula, CertificadoAutomaticoService $certificadoService): RedirectResponse
    {
        $this->autorizarMatricula($matricula);

        $quiz = $matricula->curso->quiz()->with('preguntas.opciones')->firstOrFail();

        if ($matricula->completado) {
            return redirect()->route('aula.cursos.show', $matricula);
        }

        if ($matricula->intentos_usados >= $quiz->intentos_maximos) {
            return redirect()->route('aula.cursos.show', $matricula)
                ->with('error', 'Ya agotaste el número de intentos permitidos.');
        }

        $respuestas = $request->input('respuestas', []); // ['pregunta_id' => 'opcion_id']

        $totalPreguntas = $quiz->preguntas->count();
        $correctas = 0;
        $detalle = [];

        foreach ($quiz->preguntas as $pregunta) {
            $opcionSeleccionada = $respuestas[$pregunta->id] ?? null;
            $esCorrecta = $pregunta->opciones
                ->firstWhere('id', (int) $opcionSeleccionada)?->es_correcta ?? false;

            if ($esCorrecta) {
                $correctas++;
            }

            $detalle[] = [
                'pregunta_id' => $pregunta->id,
                'opcion_seleccionada' => $opcionSeleccionada,
                'correcta' => $esCorrecta,
            ];
        }

        $nota = $totalPreguntas > 0 ? round(($correctas / $totalPreguntas) * 100, 2) : 0;
        $aprobado = $nota >= $quiz->nota_minima;

        $intento = QuizIntento::create([
            'quiz_id' => $quiz->id,
            'matricula_id' => $matricula->id,
            'numero_intento' => $matricula->intentos_usados + 1,
            'nota_obtenida' => $nota,
            'aprobado' => $aprobado,
            'respuestas' => $detalle,
            'iniciado_en' => now(),
            'finalizado_en' => now(),
        ]);

        if ($aprobado) {
            // El intento ya quedó guardado (aprobado) aunque esto falle; se aísla en
            // un try/catch para no mostrarle un error 500 al capacitado justo al
            // aprobar. El certificado SÍ se crea aunque falle el PDF (solo la
            // generación del PDF queda fuera de la transacción); si falla, queda
            // recuperable con "Regenerar PDF" desde el panel admin.
            try {
                $certificadoService->generarDesdeIntento($intento);

                return redirect()->route('aula.cursos.show', $matricula)
                    ->with('success', "¡Felicidades! Aprobaste con {$nota}%. Tu certificado ya está disponible.");
            } catch (\Throwable $e) {
                report($e);

                return redirect()->route('aula.cursos.show', $matricula)
                    ->with('success', "¡Felicidades! Aprobaste con {$nota}%. Tuvimos un problema generando tu certificado, pero ya quedó registrado tu resultado — el instituto te lo hará llegar en breve.");
            }
        }

        return redirect()->route('aula.cursos.show', $matricula)
            ->with('error', "Obtuviste {$nota}%, no alcanzaste la nota mínima ({$quiz->nota_minima}%). Intentos restantes: " . ($quiz->intentos_maximos - $matricula->fresh()->intentos_usados) . '.');
    }

    private function autorizarMatricula(Matricula $matricula): void
    {
        abort_unless(
            $matricula->capacitado_id === Auth::guard('capacitados')->id(),
            403
        );
    }
}
