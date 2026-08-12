<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\QuizIntento;
use Illuminate\Support\Facades\DB;

/**
 * Genera el certificado automáticamente cuando un capacitado aprueba el
 * quiz de un curso del aula virtual (origen = 'virtual'), reutilizando la
 * misma lógica de código único y PDF que la emisión manual.
 */
class CertificadoAutomaticoService
{
    public function __construct(
        private CertificadoPdfService $pdfService,
        private CertificadoCorreoService $correoService,
    ) {
    }

    public function generarDesdeIntento(QuizIntento $intento): Certificado
    {
        $intento->loadMissing('matricula.curso', 'matricula.capacitado');
        $matricula = $intento->matricula;

        // Transacción atómica: solo crea el certificado (con código único
        // confirmado, con reintento ante colisión) y marca la matrícula como
        // completada. El PDF se genera DESPUÉS de confirmar esto — si falla,
        // el certificado ya existe (recuperable con "Regenerar PDF" desde el
        // panel admin) en vez de quedar huérfano un archivo de un registro
        // que nunca llegó a existir.
        $certificado = DB::transaction(function () use ($intento, $matricula) {
            $certificado = Certificado::create([
                'capacitado_id' => $matricula->capacitado_id,
                'curso_id' => $matricula->curso_id,
                // Sin sesión de empleado (guard "capacitados"): el certificado
                // hereda el dueño del curso del aula virtual, no de auth().
                'user_id' => $matricula->curso->user_id,
                'origen' => 'virtual',
                'quiz_intento_id' => $intento->id,
                'codigo_unico' => (string) \Illuminate\Support\Str::uuid(),
                'fecha_emision' => now()->toDateString(),
                'fecha_vencimiento' => now()->addYear()->toDateString(),
                'intensidad_horaria' => $matricula->curso->intensidad_horaria,
                'activo' => true,
            ]);

            $certificado->guardarConCodigoUnico();

            $matricula->update([
                'completado' => true,
                'fecha_completado' => now()->toDateString(),
            ]);

            return $certificado;
        });

        // Fuera de la transacción: si esto falla, la excepción sube al llamador
        // (QuizController ya la captura y muestra un mensaje amigable), pero
        // el certificado y la matrícula completada ya quedaron guardados.
        $certificado->archivo_pdf = $this->pdfService->generarYGuardar($certificado);
        $certificado->saveQuietly();

        // El envío es una llamada de red (Resend): no debe revertir la
        // emisión ni el PDF si falla.
        $this->correoService->enviar($certificado);

        return $certificado;
    }
}
