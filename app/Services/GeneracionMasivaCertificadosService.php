<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\SolicitudCertificado;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Genera certificados en lote a partir de solicitudes de certificación
 * pendientes (creadas por la importación masiva de capacitados).
 */
class GeneracionMasivaCertificadosService
{
    /**
     * Solicitudes pendientes, con su capacitado y curso precargados para la
     * pantalla de generación masiva. whereHas('capacitado') aplica el scope
     * de propietario del capacitado: un instructor solo ve solicitudes de
     * sus propios capacitados; admin las ve todas.
     */
    public function solicitudesPendientes(): Collection
    {
        return SolicitudCertificado::pendientes()
            ->whereHas('capacitado')
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
        $errores   = [];

        foreach ($filas as $fila) {
            try {
                /**
                 * Fase 1 — transacción DB: crea el certificado y marca la solicitud
                 * como procesada de forma atómica. El PDF queda fuera de la transacción
                 * para evitar archivos huérfanos en disco si el rollback ocurre.
                 */
                $certificado = null;

                DB::transaction(function () use ($fila, $emitidoPor, &$certificado) {
                    // whereHas('capacitado'): defensa en profundidad — si alguien
                    // manipula el formulario para incluir el ID de una solicitud
                    // de un capacitado ajeno, no la encuentra y falla con 404.
                    $solicitud = SolicitudCertificado::pendientes()
                        ->whereHas('capacitado')
                        ->findOrFail($fila['solicitud_id']);

                    $certificado = Certificado::create([
                        'capacitado_id'      => $solicitud->capacitado_id,
                        'curso_id'           => $fila['curso_id'],
                        'user_id'            => $emitidoPor,
                        'emitido_por'        => $emitidoPor,
                        'codigo_unico'       => (string) Str::uuid(),
                        'fecha_emision'      => $fila['fecha_emision'],
                        'fecha_vencimiento'  => Carbon::parse($fila['fecha_emision'])->addYears($fila['anios_vigencia'])->toDateString(),
                        'intensidad_horaria' => $fila['intensidad_horaria'],
                        'modalidad'          => $fila['modalidad'],
                        'activo'             => $fila['activo'],
                    ]);

                    $certificado->guardarConCodigoUnico();

                    $solicitud->update([
                        'estado'          => SolicitudCertificado::ESTADO_PROCESADA,
                        'certificado_id'  => $certificado->id,
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
