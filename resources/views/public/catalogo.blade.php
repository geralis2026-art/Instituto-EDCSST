@extends('layouts.public')

@section('titulo', 'Catálogo de Cursos')
@section('descripcion', 'Explora todos los cursos y certificaciones que ofrece el Instituto EDCSST.')

@section('contenido')

<x-public.encabezado
    titulo="Catálogo de cursos"
    subtitulo="Programas en seguridad y salud en el trabajo con certificación verificable en línea." />

<section class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($categorias->isEmpty())
            <div class="text-center bg-white border border-dashed border-slate-300 rounded-xl py-16 px-6">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <h2 class="text-xl font-semibold text-slate-800 mb-2">Pronto publicaremos nuestros cursos</h2>
                <p class="text-slate-600">Estamos preparando el catálogo. <a href="{{ route('contacto') }}" class="text-blue-800 font-semibold underline underline-offset-2">Escríbenos</a> para conocer la oferta disponible.</p>
            </div>
        @else
            {{-- Navegación por categorías --}}
            @if($categorias->count() > 1)
                <nav aria-label="Categorías" class="mb-12">
                    <ul class="flex flex-wrap gap-2">
                        @foreach($categorias as $categoria)
                            <li>
                                <a href="#categoria-{{ $categoria->slug }}"
                                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-md bg-white border border-slate-300 text-[15px] font-medium text-slate-700 hover:border-slate-500 hover:text-slate-900 transition-colors">
                                    {{ $categoria->nombre }}
                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-2 py-0.5">{{ $categoria->cursos->count() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            @foreach($categorias as $categoria)
                <section id="categoria-{{ $categoria->slug }}" class="mb-16 last:mb-0 scroll-mt-28" aria-labelledby="titulo-{{ $categoria->slug }}">
                    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2 pb-5 mb-8 border-b border-slate-300">
                        <div>
                            <h2 id="titulo-{{ $categoria->slug }}" class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $categoria->nombre }}</h2>
                            @if($categoria->descripcion)
                                <p class="text-slate-600 mt-2 max-w-3xl">{{ $categoria->descripcion }}</p>
                            @endif
                        </div>
                        <span class="text-slate-500">
                            {{ $categoria->cursos->count() }} {{ Str::plural('curso', $categoria->cursos->count()) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categoria->cursos as $curso)
                            <article class="bg-white rounded-lg overflow-hidden border border-slate-200 flex flex-col card-gold-hover reveal delay-{{ min(($loop->index % 6) + 1, 6) }}">
                                <div class="aspect-video bg-marca-navy relative overflow-hidden">
                                    @if($curso->imagen)
                                        <img src="{{ $curso->imagen_url }}" alt="" loading="lazy" decoding="async" width="640" height="360"
                                             class="w-full h-full object-cover">
                                    @else
                                        {{-- Sin foto: portada tipográfica con el nombre del curso --}}
                                        <div class="w-full h-full flex items-end p-5 border-b-4 border-amber-400" aria-hidden="true">
                                            <span class="font-titulo text-lg font-semibold text-white leading-snug line-clamp-3">{{ $curso->nombre }}</span>
                                        </div>
                                    @endif
                                    @if($curso->destacado)
                                        <span class="absolute top-3 left-3 bg-white text-marca-navy px-2.5 py-1 rounded text-xs font-semibold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Destacado
                                        </span>
                                    @endif
                                </div>

                                <div class="p-6 flex-1 flex flex-col">
                                    <h3 class="font-semibold text-lg text-slate-900 leading-snug mb-2">{{ $curso->nombre }}</h3>
                                    <p class="text-[15px] text-slate-600 mb-5">{{ $curso->descripcion_corta }}</p>

                                    <dl class="mt-auto grid grid-cols-2 gap-3 pt-4 border-t border-slate-100 text-sm">
                                        <div>
                                            <dt class="text-slate-500">Duración</dt>
                                            <dd class="font-semibold text-slate-800">{{ $curso->duracion }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-slate-500">Intensidad</dt>
                                            <dd class="font-semibold text-slate-800">{{ $curso->intensidad_horaria }} horas</dd>
                                        </div>
                                    </dl>

                                    <a href="{{ route('contacto') }}"
                                       class="mt-5 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 text-[15px] font-semibold text-marca-navy hover:bg-marca-navy hover:text-white hover:border-marca-navy transition-colors">
                                        Solicitar información
                                        <span class="sr-only">sobre {{ $curso->nombre }}</span>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif
    </div>
</section>

@endsection
