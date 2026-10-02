@extends('layouts.public')

@section('titulo', 'Términos y condiciones')
@section('descripcion', 'Condiciones de uso del sitio web del Instituto EDCSST: servicios, uso permitido, verificación de certificados, propiedad intelectual y ley aplicable.')

@section('contenido')
@php
    $c      = config('politicas');
    $correo = $configSitio->correo_contacto;
@endphp

<x-public.documento-legal
    titulo="Términos y condiciones"
    subtitulo="Las reglas para usar este sitio web. Léelas con calma: al usarlo, las aceptas."
    :indice="[
        'quienes'       => 'Quiénes somos y alcance',
        'servicios'     => 'Qué puedes hacer en el sitio',
        'uso'           => 'Uso permitido',
        'certificados'  => 'Certificados y su verificación',
        'propiedad'     => 'Propiedad intelectual',
        'datos'         => 'Datos personales',
        'responsabilidad' => 'Disponibilidad y responsabilidad',
        'terceros'      => 'Enlaces a otros sitios',
        'ley'           => 'Ley aplicable y jurisdicción',
        'cambios'       => 'Cambios y contacto',
    ]">

    <h2 id="quienes">1. Quiénes somos y alcance</h2>
    <p>
        Este sitio web es operado por <strong>{{ $c['razon_social'] }}</strong> (en adelante, «{{ $c['nombre_corto'] }}»),
        NIT {{ $c['nit'] }}, con domicilio en {{ $c['ciudad'] }}. Estos términos regulan el acceso y el uso del sitio
        {{ url('/') }} y de sus servicios. Si no estás de acuerdo con ellos, te pedimos no usar el sitio.
    </p>

    <h2 id="servicios">2. Qué puedes hacer en el sitio</h2>
    <ul>
        <li>Conocer el instituto y su catálogo de cursos.</li>
        <li>Consultar y descargar tus certificados con tu número de documento o con el código del certificado.</li>
        <li>Verificar si un certificado fue emitido por el instituto.</li>
        <li>Escribirnos por el formulario de contacto.</li>
        <li>Inscribirte a cursos mediante el enlace de registro que te comparta el instituto.</li>
    </ul>
    <p>Para usar el sitio no necesitas crear una cuenta. El panel de administración es solo para el personal autorizado.</p>

    <h2 id="uso">3. Uso permitido</h2>
    <p>Te comprometes a usar el sitio de buena fe y de acuerdo con la ley. En particular, no está permitido:</p>
    <ul>
        <li>Suplantar a otra persona, o ingresar datos falsos o de terceros sin su autorización.</li>
        <li>Recorrer o probar códigos o números de documento de forma masiva o automática para obtener información de otras personas.</li>
        <li>Intentar acceder sin permiso al panel de administración, a otros usuarios o a los sistemas del instituto.</li>
        <li>Enviar mensajes automáticos, spam o contenido ilícito, ofensivo o que infrinja derechos de terceros.</li>
        <li>Interferir con el funcionamiento del sitio, o eludir sus medidas de seguridad y sus límites de uso.</li>
    </ul>
    <p>
        Podemos limitar o bloquear el acceso de quien incumpla estas reglas, y poner los hechos en conocimiento de las
        autoridades cuando corresponda.
    </p>

    <h2 id="certificados">4. Certificados y su verificación</h2>
    <ul>
        <li>Cada certificado tiene un código único. La página de <a href="{{ route('verificar') }}">verificación</a> confirma si ese código corresponde a un certificado emitido por el instituto y muestra su información básica y su estado.</li>
        <li>Un certificado puede estar <strong>vigente</strong>, <strong>vencido</strong> o <strong>desactivado</strong>. La verificación siempre indica el estado actual.</li>
        <li>Por privacidad, la verificación pública muestra el documento del titular solo de forma parcial.</li>
        <li>La verificación en línea es un servicio informativo: confirma la emisión, pero no sustituye la valoración que haga cada empresa o entidad sobre el certificado para sus propios fines.</li>
        <li>Si notas un error en un certificado, escríbenos para corregirlo.</li>
    </ul>

    <h2 id="propiedad">5. Propiedad intelectual</h2>
    <p>
        El nombre y el logotipo del instituto, los textos, las imágenes, el diseño del sitio y los contenidos de los cursos son
        de {{ $c['nombre_corto'] }} o de quienes nos los han licenciado, y están protegidos por las normas
        de propiedad intelectual. No puedes copiarlos, modificarlos ni usarlos con fines comerciales sin nuestra autorización
        previa y por escrito. Puedes descargar y conservar los certificados que te correspondan.
    </p>

    <h2 id="datos">6. Datos personales</h2>
    <p>
        El tratamiento de tus datos personales se rige por nuestra <a href="{{ route('politica.privacidad') }}">política de privacidad</a>,
        y el uso de cookies por la <a href="{{ route('politica.cookies') }}">política de cookies</a>. Ambas hacen parte de estos términos.
    </p>

    <h2 id="responsabilidad">7. Disponibilidad y responsabilidad</h2>
    <p>
        Hacemos lo posible para que el sitio esté disponible y la información sea correcta y esté actualizada, pero no
        garantizamos que funcione sin interrupciones ni errores. Podemos suspenderlo por mantenimiento o por razones de
        seguridad. En la medida que la ley lo permita, {{ $c['nombre_corto'] }} no responde por daños derivados de interrupciones
        ajenas a su control, del uso indebido del sitio o del uso que otros hagan de la información que tú mismo compartas.
        Nada de lo anterior limita los derechos que la ley reconoce a los consumidores ni a los titulares de datos personales.
    </p>

    <h2 id="terceros">8. Enlaces a otros sitios</h2>
    <p>
        El sitio incluye enlaces a servicios de terceros, como WhatsApp o redes sociales. No controlamos esos sitios ni somos
        responsables de su contenido o de sus políticas de privacidad.
    </p>

    <h2 id="ley">9. Ley aplicable y jurisdicción</h2>
    <p>
        Estos términos se rigen por las leyes de la República de Colombia. Cualquier diferencia se resolverá ante los jueces
        competentes de Colombia, sin perjuicio de las acciones que la ley permita ante la Superintendencia de Industria y Comercio.
    </p>

    <h2 id="cambios">10. Cambios y contacto</h2>
    <p>
        Podemos modificar estos términos. La versión vigente es siempre la publicada en esta página, con su fecha.
        Si tienes dudas, escríbenos
        @if($correo) a <a href="mailto:{{ $correo }}">{{ $correo }}</a>@else por el <a href="{{ route('contacto') }}">formulario de contacto</a>@endif.
    </p>
</x-public.documento-legal>
@endsection
