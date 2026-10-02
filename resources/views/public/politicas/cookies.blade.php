@extends('layouts.public')

@section('titulo', 'Política de cookies')
@section('descripcion', 'Qué cookies usa el sitio del Instituto EDCSST, para qué sirven y cómo puedes gestionarlas.')

@section('contenido')
<x-public.documento-legal
    titulo="Política de cookies"
    subtitulo="Qué cookies usamos, para qué y cómo puedes elegir."
    :indice="[
        'que-son'    => 'Qué son las cookies',
        'que-usamos' => 'Qué usamos en este sitio',
        'tabla'      => 'Detalle de cada una',
        'elegir'     => 'Cómo elegir y cambiar tu decisión',
        'navegador'  => 'Configuración del navegador',
        'cambios'    => 'Cambios y contacto',
    ]">

    <h2 id="que-son">1. Qué son las cookies</h2>
    <p>
        Las cookies son pequeños archivos que un sitio web guarda en tu navegador para recordar información, por ejemplo
        que tu sesión sigue abierta. Algunas son necesarias para que el sitio funcione y otras sirven para medir el uso o
        mostrar publicidad.
    </p>

    <h2 id="que-usamos">2. Qué usamos en este sitio</h2>
    <ul>
        <li><strong>Cookies técnicas necesarias:</strong> mantienen tu sesión y protegen los formularios. Sin ellas el sitio no funciona de forma segura.</li>
        <li><strong>Google reCAPTCHA (opcional):</strong> protege el formulario de contacto contra mensajes automáticos. Solo se carga si tú lo permites.</li>
        <li><strong>No usamos</strong> cookies de analítica, de publicidad ni de seguimiento, y no hay píxeles de redes sociales.</li>
    </ul>
    <p>
        Las cookies técnicas se pueden guardar desde que entras al sitio, porque son imprescindibles para el funcionamiento
        y la seguridad. Aun así te informamos de ellas y de las opciones que tienes.
    </p>

    <h2 id="tabla">3. Detalle de cada una</h2>
    <div class="tabla-legal">
        <table>
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Quién la crea</th>
                    <th scope="col">Para qué sirve</th>
                    <th scope="col">Cuánto dura</th>
                    <th scope="col">Tipo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">{{ config('session.cookie') }}</th>
                    <td>Este sitio</td>
                    <td>Mantiene la sesión y recuerda mensajes entre páginas, como la confirmación de un formulario.</td>
                    <td>Hasta {{ config('session.lifetime') }} minutos sin actividad.</td>
                    <td>Necesaria</td>
                </tr>
                <tr>
                    <th scope="row">XSRF-TOKEN</th>
                    <td>Este sitio</td>
                    <td>Protege los formularios contra envíos falsificados desde otros sitios.</td>
                    <td>Hasta {{ config('session.lifetime') }} minutos.</td>
                    <td>Necesaria</td>
                </tr>
                <tr>
                    <th scope="row">remember_web_…</th>
                    <td>Este sitio</td>
                    <td>Solo para el personal del instituto: mantiene abierta la sesión del panel si marca «Recordarme». Los visitantes no la reciben.</td>
                    <td>Larga duración, solo si se elige.</td>
                    <td>Necesaria (personal)</td>
                </tr>
                <tr>
                    <th scope="row">Cookies de Google (por ejemplo, _GRECAPTCHA)</th>
                    <td>Google LLC</td>
                    <td>Distinguir a personas de programas automáticos en el formulario de contacto. Google puede recibir datos como tu dirección IP y el comportamiento del navegador.</td>
                    <td>La define Google. Consulta su política de privacidad.</td>
                    <td>Opcional: solo con tu permiso</td>
                </tr>
                <tr>
                    <th scope="row">edcsst_cookies</th>
                    <td>Este sitio (almacenamiento local del navegador, no es una cookie)</td>
                    <td>Recuerda lo que elegiste en el aviso de cookies para no preguntarte en cada visita.</td>
                    <td>Hasta que la borres.</td>
                    <td>Necesaria</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>
        Además, las tipografías del sitio las entrega Bunny Fonts (fonts.bunny.net). No usa cookies, pero al cargarse recibe tu
        dirección IP, como ocurre con cualquier contenido que se pide a un servidor externo.
    </p>

    <h2 id="elegir">4. Cómo elegir y cambiar tu decisión</h2>
    <p>
        La primera vez que entras, el aviso de cookies te deja elegir entre <strong>«Solo las necesarias»</strong> y
        <strong>«Aceptar todas»</strong>. Con la primera, Google reCAPTCHA no se carga; en el formulario de contacto podrás
        activarlo cuando quieras enviar un mensaje, o escribirnos por correo o WhatsApp.
    </p>
    <p>
        Puedes cambiar tu decisión cuando quieras con el botón
        <button type="button" data-abrir-cookies class="font-semibold text-blue-800 underline underline-offset-2 hover:text-marca-navy">Preferencias de cookies</button>
        que está al final de todas las páginas. Para retirar el permiso que ya diste a reCAPTCHA, elige allí «Solo las necesarias» y borra las cookies de Google en tu navegador.
    </p>

    <h2 id="navegador">5. Configuración del navegador</h2>
    <p>
        También puedes bloquear o borrar las cookies desde la configuración de tu navegador. Si bloqueas las cookies técnicas,
        formularios como el de contacto o el de registro pueden dejar de funcionar. Consulta la ayuda de tu navegador
        (Chrome, Edge, Firefox, Safari) para ver cómo hacerlo.
    </p>

    <h2 id="cambios">6. Cambios y contacto</h2>
    <p>
        Si en el futuro usamos otras cookies, actualizaremos esta página y te volveremos a pedir tu elección antes de activarlas.
        El tratamiento de tus datos se explica en la <a href="{{ route('politica.privacidad') }}">política de privacidad</a>.
        Para cualquier duda puedes usar el <a href="{{ route('contacto') }}">formulario de contacto</a>.
    </p>
</x-public.documento-legal>
@endsection
