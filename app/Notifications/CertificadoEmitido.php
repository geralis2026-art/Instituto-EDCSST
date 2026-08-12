<?php

namespace App\Notifications;

use App\Models\Certificado;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Envía el certificado en PDF por correo al capacitado cuando se emite
 * (manual o automáticamente). Ver CertificadoCorreoService, que decide
 * cuándo llamar a esto y evita romper el flujo si el envío falla.
 */
class CertificadoEmitido extends Notification
{
    use Queueable;

    public function __construct(private Certificado $certificado)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->certificado->loadMissing('curso');
        $pdf = Storage::disk('certificados')->get($this->certificado->archivo_pdf);

        $mensaje = (new MailMessage)
            ->subject('Tu certificado - ' . $this->certificado->curso->nombre)
            ->greeting('¡Hola ' . $notifiable->nombre_completo . '!')
            ->line('Adjunto encontrarás tu certificado del curso "' . $this->certificado->curso->nombre . '".')
            ->line('Código de verificación: ' . $this->certificado->codigo_unico)
            ->action('Verificar autenticidad', route('verificar'))
            ->line('Guarda este correo, el PDF adjunto es tu certificado oficial.');

        if ($pdf !== false) {
            $mensaje->attachData($pdf, $this->certificado->codigo_unico . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mensaje;
    }
}
