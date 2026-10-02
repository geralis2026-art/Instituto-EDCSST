@props([
    'titulo',
    'subtitulo' => null,
    'indice' => [],   // ['id-de-seccion' => 'Texto del índice']
])

{{-- Marco común de las páginas legales: encabezado, índice de la página y cuerpo con estilo de documento --}}
<x-public.encabezado :titulo="$titulo" :subtitulo="$subtitulo" etiqueta="Información legal" />

<section class="py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <p class="text-sm text-slate-600 mb-8">
            Versión {{ config('politicas.version') }} · Vigente desde el {{ config('politicas.vigente_desde') }}
        </p>

        @if(count($indice))
            <nav aria-labelledby="indice-legal" class="mb-10 rounded-xl border border-slate-200 bg-slate-50 p-5 sm:p-6">
                <h2 id="indice-legal" class="font-display text-base font-semibold text-slate-900">En esta página</h2>
                <ol class="mt-3 grid sm:grid-cols-2 gap-x-8 gap-y-1.5 list-decimal pl-5 text-[15px]">
                    @foreach($indice as $id => $texto)
                        <li><a href="#{{ $id }}" class="text-blue-800 underline underline-offset-2 hover:text-marca-navy">{{ $texto }}</a></li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <article class="documento-legal">
            {{ $slot }}
        </article>

        <aside class="mt-14 rounded-xl border border-slate-200 bg-white p-5 sm:p-6 text-[15px] text-slate-700" aria-label="Documentos relacionados">
            <p class="font-semibold text-slate-900">Documentos relacionados</p>
            <ul class="mt-2 flex flex-wrap gap-x-6 gap-y-1.5">
                <li><a href="{{ route('politica.privacidad') }}" class="text-blue-800 underline underline-offset-2">Política de privacidad</a></li>
                <li><a href="{{ route('terminos') }}" class="text-blue-800 underline underline-offset-2">Términos y condiciones</a></li>
                <li><a href="{{ route('politica.cookies') }}" class="text-blue-800 underline underline-offset-2">Política de cookies</a></li>
            </ul>
        </aside>
    </div>
</section>
