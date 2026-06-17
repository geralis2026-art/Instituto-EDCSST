<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\SolicitudCertificado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Genera certificados en lote a partir de solicitudes de certificación
 * pendientes (creadas por la importación masiva de capacitados).
 */
class GeneracionMasivaCertificadosService
{
    /** Solicitudes pendientes, con su capacitado y curso precargados para la pantalla de generación masiva. */
    public function solicitudesPendientes()
    {
        return SolicitudCertificado::pendientes()
            ->with(['capacitado', 'curso'])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Genera un certificado por cada fila incluida. Las filas que fallan se
     * registran en `errores` y no detienen el procesamiento de las demás.
     *
     * @param  array<int, array{
     *     solicitud_id: int,
     *     curso_id: int,
     *     fecha_emision: string,
     *     intensidad_horaria: int,
     *     modalidad: ?string,
     *     anios_vigencia: int,
     * }>  $filas
     * @return array{generados: int, errores: array<int, string>}
     */
    public function generar(array $filas, int $emitidoPor, CertificadoPdfService $pdfService): array
    {
        $generados = 0;
        $errores = [];

        foreach ($filas as $fila) {
            try {
                /**
                 * Fase 1 — transacción DB: crea el certificado y marca la solicitud
                 * como procesada de forma atómica. El PDF queda fuera de la transacción
                 * para evitar archivos huérfanos en disco si el rollback ocurre.
                 */
                $certificado = null;

                DB::transaction(function () use ($fila, $emitidoPor, &$certificado) {
                    $solicitud = SolicitudCertificado::pendientes()->findOrFail($fila['solicitud_id']);

                    $certificado = Certificado::create([
                        'capacitado_id' => $solicitud->capacitado_id,
                        'curso_id' => $fila['curso_id'],
                        'emitido_por' => $emitidoPor,
                        'codigo_unico' => (string) Str::uuid(),
                        'fecha_emision' => $fila['fecha_emision'],
                        'fecha_vencimiento' => Carbon::parse($fila['fecha_emision'])->addYears($fila['anios_vigencia'])->toDateString(),
                        'intensidad_horaria' => $fila['intensidad_horaria'],
                        'modalidad' => $fila['modalidad'],
                        'activo' => $fila['activo'],
                    ]);

                    $certificado->codigo_unico = Certificado::generarCodigoUnico();
                    $certificado->saveQuietly();

                    $solicitud->update([
                        'estado' => 'procesada',
                        'certificado_id' => $certificado->id,
                    ]);
                });

                /**
                 * Fase 2 — generación del PDF fuera de la transacción.
                 * Si falla, el certificado queda sin PDF pero es recuperable:
                 * verPdf() lo regenera al vuelo bajo demanda.
                 */
                $certificado->archivo_pdf = $pdfService->generarYGuardar($certificado);
                $certificado->saveQuietly();

                $generados++;
            } catch (\Throwable $e) {
                $errores[] = "Solicitud #{$fila['solicitud_id']}: " . $e->getMessage();
            }
        }

        return ['generados' => $generados, 'errores' => $errores];
    }
}
