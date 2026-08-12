<?php

namespace App\Notifications;

use App\Models\Curso;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifica al capacitado que se le asignó un curso en el aula virtual.
 * Si `esPrimerAcceso` es true (primera matrícula del capacitado, cuenta
 * recién creada) se incluyen las instrucciones de ingreso: la contraseña
 * inicial es su número de documento, y el primer ingreso exige cambiarla.
 */
class CapacitadoBienvenida extends Notification
{
    use Queueable;

    public function __construct(
        private Curso $curso,
        private bool $esPrimerAcceso = false,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mensaje = (new MailMessage)
            ->subject('Ya tienes acceso al Aula Virtual - ' . $this->curso->nombre)
            ->greeting('¡Hola ' . $notifiable->nombre_completo . '!')
            ->line('Se te ha asignado el curso "' . $this->curso->nombre . '" en el Aula Virtual del Instituto EDCSST.');

        if ($this->esPrimerAcceso) {
            $mensaje
                ->line('Ya puedes ingresar con estas credenciales:')
                ->line('Correo: ' . $notifiable->correo)
                ->line('Contraseña inicial: tu número de documento')
                ->line('Por seguridad, te pediremos crear una nueva contraseña (distinta a tu documento) la primera vez que ingreses.');
        }

        return $mensaje
            ->action('Ir al Aula Virtual', route('login'))
            ->line('Si tienes dudas, contáctanos respondiendo este correo.');
    }
}
