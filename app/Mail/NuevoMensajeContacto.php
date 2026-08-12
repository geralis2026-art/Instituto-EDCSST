<?php

namespace App\Mail;

use App\Models\Mensaje;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Avisa al instituto que llegó un mensaje nuevo por el formulario de contacto público. */
class NuevoMensajeContacto extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Mensaje $mensaje)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje de contacto - ' . $this->mensaje->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.nuevo-mensaje-contacto',
        );
    }
}
