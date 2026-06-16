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
     *     codigo_unico: ?string,
     *     archivo_pdf: ?\Illuminate\Http\UploadedFile,
     * }>  $filas
     * @return array{generados: int, errores: array<int, string>}
     */
    public function generar(array $filas, int $emitidoPor, CertificadoPdfService $pdfService): array
    {
        $generados = 0;
        $errores = [];

        foreach ($filas as $fila) {
            try {
                DB::transaction(function () use ($fila, $emitidoPor, $pdfService) {
                    $solicitud = SolicitudCertificado::pendientes()->findOrFail($fila['solicitud_id']);

                    $codigoManual = $fila['codigo_unico'] ?: null;

                    $datos = [
                        'capacitado_id' => $solicitud->capacitado_id,
                        'curso_id' => $fila['curso_id'],
                        'emitido_por' => $emitidoPor,
                        'codigo_unico' => $codigoManual ?? (string) Str::uuid(),
                        'fecha_emision' => $fila['fecha_emision'],
                        'fecha_vencimiento' => Carbon::parse($fila['fecha_emision'])->addYear()->toDateString(),
                        'intensidad_horaria' => $fila['intensidad_horaria'],
                        'modalidad' => $fila['modalidad'],
                        'activo' => true,
                    ];

                    if ($fila['archivo_pdf']) {
                        $datos['archivo_pdf'] = $fila['archivo_pdf']->store('certificados', 'certificados');
                    }

                    $certificado = Certificado::create($datos);

                    if (!$codigoManual) {
                        $certificado->codigo_unico = Certificado::generarCodigoUnico();
                    }

                    if (!$fila['archivo_pdf']) {
                        $certificado->archivo_pdf = $pdfService->generarYGuardar($certificado);
                    }

                    $certificado->saveQuietly();

                    $solicitud->update([
                        'estado' => 'procesada',
                        'certificado_id' => $certificado->id,
                    ]);
                });

                $generados++;
            } catch (\Throwable $e) {
                $errores[] = "Solicitud #{$fila['solicitud_id']}: " . $e->getMessage();
            }
        }

        return ['generados' => $generados, 'errores' => $errores];
    }
}
