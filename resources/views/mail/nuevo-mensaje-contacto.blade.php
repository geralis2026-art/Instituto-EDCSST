<x-mail::message>
# Nuevo mensaje de contacto

**Nombre:** {{ $mensaje->nombre }}<br>
**Correo:** {{ $mensaje->correo }}

{{ $mensaje->mensaje }}

<x-mail::button :url="route('admin.mensajes.show', $mensaje)">
Ver en el panel
</x-mail::button>

Saludos,<br>
Instituto EDCSST
</x-mail::message>
