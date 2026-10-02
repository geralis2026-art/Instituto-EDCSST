@extends('layouts.public')

@section('titulo', 'Política de privacidad')
@section('descripcion', 'Política de tratamiento de datos personales del Instituto EDCSST: qué datos recolectamos, para qué, cómo ejercer tus derechos y cómo presentar una consulta o reclamo.')

@section('contenido')
@php
    $c       = config('politicas');
    $correo  = $configSitio->correo_contacto;
    $tel     = $configSitio->telefono;
    $dir     = $configSitio->direccion;
@endphp

<x-public.documento-legal
    titulo="Política de privacidad"
    subtitulo="Política de tratamiento de datos personales. Te explicamos qué datos recolectamos, para qué los usamos y cómo ejercer tus derechos."
    :indice="[
        'responsable'   => 'Quién es el responsable',
        'marco'         => 'Marco legal',
        'datos'         => 'Qué datos recolectamos y para qué',
        'autorizacion'  => 'Tu autorización',
        'sensibles'     => 'Datos sensibles',
        'menores'       => 'Menores de edad',
        'derechos'      => 'Tus derechos',
        'procedimiento' => 'Cómo presentar una consulta o reclamo',
        'terceros'      => 'Proveedores que intervienen',
        'seguridad'     => 'Seguridad',
        'conservacion'  => 'Cuánto tiempo conservamos los datos',
        'cambios'       => 'Cambios en esta política',
    ]">

    <h2 id="responsable">1. Quién es el responsable</h2>
    <p>
        El responsable del tratamiento de tus datos personales es <strong>{{ $c['razon_social'] }}</strong>
        (en adelante, «{{ $c['nombre_corto'] }}»), con NIT {{ $c['nit'] }}, domiciliada en {{ $c['ciudad'] }}.
    </p>
    <ul>
        @if($dir)<li><strong>Dirección:</strong> {{ $dir }}</li>@endif
        @if($correo)<li><strong>Correo para asuntos de datos personales:</strong> <a href="mailto:{{ $correo }}">{{ $correo }}</a></li>@endif
        @if($tel)<li><strong>Teléfono:</strong> {{ $tel }}</li>@endif
        <li><strong>Sitio web:</strong> {{ url('/') }}</li>
    </ul>

    <h2 id="marco">2. Marco legal</h2>
    <p>
        Esta política se rige por el artículo 15 de la Constitución Política de Colombia, la Ley Estatutaria 1581 de 2012
        (régimen general de protección de datos personales), el Decreto 1377 de 2013 (compilado en el Decreto 1074 de 2015)
        y las demás normas que los modifiquen o complementen. La autoridad de protección de datos en Colombia es la
        Superintendencia de Industria y Comercio (SIC).
    </p>

    <h2 id="datos">3. Qué datos recolectamos y para qué</h2>
    <p>Solo pedimos los datos necesarios para cada servicio. Esto es lo que recolectamos y con qué finalidad:</p>

    <div class="tabla-legal">
        <table>
            <thead>
                <tr><th scope="col">Dónde</th><th scope="col">Datos</th><th scope="col">Finalidad</th></tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Formulario de contacto</th>
                    <td>Nombre, correo electrónico, mensaje y dirección IP.</td>
                    <td>Responder tu consulta. La dirección IP se usa únicamente para seguridad y para prevenir el abuso del formulario.</td>
                </tr>
                <tr>
                    <th scope="row">Registro de inscripción (enlace que te comparte el instituto)</th>
                    <td>Nombre completo, tipo y número de documento, cursos y modalidad elegidos. Correo electrónico y teléfono, que son opcionales.</td>
                    <td>Gestionar tu inscripción, emitir tu certificado y permitir que se verifique su autenticidad.</td>
                </tr>
                <tr>
                    <th scope="row">Certificados</th>
                    <td>Código del certificado, curso, intensidad horaria, fechas de emisión y vencimiento, y el archivo PDF.</td>
                    <td>Emitirlo, entregártelo y permitir a terceros comprobar que es auténtico.</td>
                </tr>
                <tr>
                    <th scope="row">Consulta y verificación en línea</th>
                    <td>El número de documento o el código del certificado que escribes.</td>
                    <td>Localizar el certificado y mostrarte el resultado. Quien verifica un código ve el nombre del titular, los tres últimos dígitos de su documento, el curso, la intensidad horaria y las fechas.</td>
                </tr>
                <tr>
                    <th scope="row">Navegación por el sitio</th>
                    <td>Cookies técnicas y dirección IP en los registros del servidor.</td>
                    <td>Que el sitio funcione y sea seguro. Más detalle en la <a href="{{ route('politica.cookies') }}">política de cookies</a>.</td>
                </tr>
                <tr>
                    <th scope="row">Personal del instituto</th>
                    <td>Nombre, correo electrónico y contraseña (guardada de forma cifrada).</td>
                    <td>Dar acceso al panel de administración.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p>
        No vendemos tus datos ni los usamos para publicidad. No los usamos para finalidades distintas a las anteriores
        sin pedirte antes una nueva autorización.
    </p>

    <h2 id="autorizacion">4. Tu autorización</h2>
    <p>
        Tratamos tus datos personales con tu autorización previa, expresa e informada. En los formularios del sitio
        la das marcando una casilla que nunca viene marcada de antemano. Guardamos la fecha y la versión de esta
        política vigente cuando la diste, como prueba de la autorización.
    </p>
    <p>
        Cuando tus datos los registra el personal del instituto (por ejemplo, en una inscripción presencial),
        la autorización se recoge por ese medio. Puedes revocarla en cualquier momento, salvo cuando exista un deber
        legal o contractual que obligue a conservar el dato.
    </p>

    <h2 id="sensibles">5. Datos sensibles</h2>
    <p>
        Son sensibles los datos que afectan tu intimidad o cuyo uso indebido puede discriminarte, como los de salud,
        origen racial o étnico, convicciones políticas o religiosas, o los datos biométricos.
        <strong>A través de este sitio web no solicitamos datos sensibles.</strong>
        Si en algún proceso de formación fuera necesario registrar alguno, te avisaremos, responderlo será siempre
        facultativo y necesitaremos tu autorización explícita. Te pedimos no incluir datos de este tipo en el campo de mensaje
        del formulario de contacto.
    </p>

    <h2 id="menores">6. Menores de edad</h2>
    <p>
        El sitio y sus formularios están dirigidos a personas mayores de edad. No recolectamos de forma intencional datos
        de niños, niñas o adolescentes. Si detectamos que se registraron sin la autorización de sus representantes legales,
        los eliminaremos.
    </p>

    <h2 id="derechos">7. Tus derechos</h2>
    <p>Como titular de los datos tienes derecho a:</p>
    <ul>
        <li>Conocer, actualizar y rectificar tus datos personales.</li>
        <li>Solicitar prueba de la autorización que nos diste.</li>
        <li>Ser informado, si lo pides, del uso que se le ha dado a tus datos.</li>
        <li>Presentar quejas ante la Superintendencia de Industria y Comercio por infracciones a la ley.</li>
        <li>Revocar la autorización y/o solicitar la supresión de tus datos cuando no se respeten los principios, derechos y garantías legales.</li>
        <li>Acceder de forma gratuita a tus datos personales que hayan sido objeto de tratamiento.</li>
    </ul>

    <h2 id="procedimiento">8. Cómo presentar una consulta o reclamo</h2>
    <p>
        Para ejercer tus derechos escríbenos
        @if($correo) a <a href="mailto:{{ $correo }}">{{ $correo }}</a>@else por el <a href="{{ route('contacto') }}">formulario de contacto</a>@endif
        @if($tel) o llámanos al {{ $tel }}@endif.
        Indícanos tu nombre, tu número de documento, qué pides y cómo contactarte, para poder verificar que eres el titular.
    </p>
    <ul>
        <li><strong>Consultas</strong> (conocer qué datos tuyos tenemos): las respondemos en máximo 10 días hábiles. Si no podemos hacerlo en ese plazo, te avisaremos el motivo y la nueva fecha, que no superará 5 días hábiles más.</li>
        <li><strong>Reclamos</strong> (corregir, actualizar o suprimir datos, o revocar la autorización): los respondemos en máximo 15 días hábiles. Si necesitamos más tiempo, te lo informaremos con el motivo, sin superar 8 días hábiles adicionales. Si tu solicitud está incompleta, te pediremos lo que falte dentro de los 5 días siguientes.</li>
    </ul>
    <p>
        Solo después de haber agotado este trámite con nosotros puedes presentar una queja ante la Superintendencia de
        Industria y Comercio.
    </p>

    <h2 id="terceros">9. Proveedores que intervienen</h2>
    <p>Para prestar el servicio nos apoyamos en proveedores que tratan datos por nuestra cuenta:</p>
    <ul>
        <li><strong>Alojamiento web y correo:</strong> Hostinger, donde se guarda la información del sitio.</li>
        <li><strong>Google reCAPTCHA:</strong> protege el formulario de contacto contra mensajes automáticos. Solo se carga si tú decides activarlo.</li>
        <li><strong>Bunny Fonts (fonts.bunny.net):</strong> entrega las tipografías del sitio. Al cargarlas recibe tu dirección IP; no usa cookies.</li>
        <li><strong>WhatsApp y redes sociales:</strong> el sitio enlaza a ellas. Si las abres, te rige la política de cada plataforma.</li>
    </ul>
    <p>
        Algunos de estos proveedores pueden almacenar o procesar datos en servidores fuera de Colombia. En ese caso actúan como
        encargados del tratamiento y {{ $c['nombre_corto'] }} sigue siendo responsable de tus datos frente a ti.
    </p>

    <h2 id="seguridad">10. Seguridad</h2>
    <p>
        Protegemos los datos con medidas técnicas y administrativas razonables: conexión cifrada (HTTPS), acceso al panel
        solo para personal autorizado con usuario y contraseña, permisos según el rol de cada persona, contraseñas guardadas
        de forma cifrada y límites a los intentos repetidos de acceso. Ningún sistema es completamente infalible; si ocurriera
        un incidente que afecte tus datos, actuaremos conforme a la ley, incluido el aviso a la autoridad cuando corresponda.
    </p>

    <h2 id="conservacion">11. Cuánto tiempo conservamos los datos</h2>
    <p>
        Conservamos los datos durante el tiempo necesario para cumplir las finalidades de esta política y las obligaciones
        legales o contractuales aplicables. Los certificados emitidos se conservan mientras deban poder verificarse.
        Cuando ya no sean necesarios, los suprimimos o los anonimizamos.
    </p>

    <h2 id="cambios">12. Cambios en esta política</h2>
    <p>
        Podemos actualizar esta política, por ejemplo por cambios legales o en el sitio. Publicaremos siempre aquí la versión
        vigente con su fecha. Si el cambio altera de forma sustancial las finalidades para las que usamos tus datos,
        te pediremos una nueva autorización.
    </p>
</x-public.documento-legal>
@endsection
