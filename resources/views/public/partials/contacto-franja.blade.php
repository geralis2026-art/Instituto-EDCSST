{{-- Cierre de página con los datos de contacto reales (en vez de una caja genérica de "¿Tienes preguntas?") --}}
@php
    $telefono = $configSitio->telefono;
    $whatsapp = $configSitio->whatsapp ? preg_replace('/\D/', '', $configSitio->whatsapp) : null;
    $correo   = $configSitio->correo_contacto;
@endphp

<section class="border-t border-slate-200 bg-white" aria-labelledby="titulo-contacto-franja">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <div class="lg:col-span-4">
            <h2 id="titulo-contacto-franja" class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $titulo ?? 'Inscripciones e información' }}</h2>
            <p class="mt-3 text-slate-600">Atendemos de lunes a viernes, de 8:00 a. m. a 6:00 p. m.</p>
        </div>

        <dl class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden">
            @if($whatsapp)
                <div class="bg-white p-5">
                    <dt class="text-sm text-slate-500">WhatsApp</dt>
                    <dd class="mt-1">
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="font-semibold text-slate-900 hover:text-green-700 underline decoration-slate-300 underline-offset-4 hover:decoration-green-600">
                            Escribir un mensaje
                        </a>
                    </dd>
                </div>
            @endif
            @if($telefono)
                <div class="bg-white p-5">
                    <dt class="text-sm text-slate-500">Teléfono</dt>
                    <dd class="mt-1">
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $telefono) }}" class="font-semibold text-slate-900 tabular-nums underline decoration-slate-300 underline-offset-4 hover:decoration-slate-900">{{ $telefono }}</a>
                    </dd>
                </div>
            @endif
            <div class="bg-white p-5">
                <dt class="text-sm text-slate-500">{{ $correo ? 'Correo' : 'Formulario' }}</dt>
                <dd class="mt-1">
                    @if($correo)
                        <a href="mailto:{{ $correo }}" class="font-semibold text-slate-900 [overflow-wrap:anywhere] underline decoration-slate-300 underline-offset-4 hover:decoration-slate-900">{{ $correo }}</a>
                    @else
                        <a href="{{ route('contacto') }}" class="font-semibold text-slate-900 underline decoration-slate-300 underline-offset-4 hover:decoration-slate-900">Enviar un mensaje</a>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</section>
