<?php

namespace App\Services;

use App\Models\Certificado;
use App\Notifications\CertificadoEmitido;
use Illuminate\Support\Facades\Log;

/**
 * Envía el certificado por correo al capacitado. Aislado en un servicio
 * para que el fallo del envío (ej. Resend caído) nunca rompa el flujo de
 * emisión del certificado — solo se registra en el log.
 */
class CertificadoCorreoService
{
    /** @return bool true si se envió (o se intentó sin excepción), false si no había con qué enviarlo. */
    public function enviar(Certificado $certificado): bool
    {
        $certificado->loadMissing('capacitado');

        if (!$certificado->capacitado?->correo || !$certificado->archivo_pdf) {
            return false;
        }

        try {
            $certificado->capacitado->notify(new CertificadoEmitido($certificado));

            return true;
        } catch (\Throwable $e) {
            report($e);
            Log::warning('No se pudo enviar el certificado por correo', [
                'certificado_id' => $certificado->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
