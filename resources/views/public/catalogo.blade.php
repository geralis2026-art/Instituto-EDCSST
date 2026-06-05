@extends('layouts.public')

@section('titulo', 'Catálogo de Cursos')
@section('descripcion', 'Explora todos los cursos y certificaciones que ofrece el Instituto EDCSST.')

@section('contenido')

{{-- Encabezado --}}
<section class="bg-blue-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold mb-3">Catálogo de Cursos</h1>
        <p class="text-blue-100 text-lg">Encuentra el curso ideal para tu desarrollo profesional</p>
    </div>
</section>

{{-- Contenido principal --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($categorias->isEmpty())
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Pronto publicaremos nuestros cursos</h2>
                <p class="text-gray-500">Estamos preparando el catálogo. Visítanos pronto.</p>
            </div>
        @else
            {{-- Recorrer cada categoría --}}
            @foreach($categorias as $categoria)
                <div class="mb-12 last:mb-0">
                    {{-- Encabezado de categoría --}}
                    <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-blue-100">
                        <div>
                            <h2 class="text-2xl font-bold text-blue-900">{{ $categoria->nombre }}</h2>
                            @if($categoria->descripcion)
                                <p class="text-sm text-gray-600 mt-1">{{ $categoria->descripcion }}</p>
                            @endif
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                            {{ $categoria->cursos->count() }} {{ Str::plural('curso', $categoria->cursos->count()) }}
                        </span>
                    </div>

                    {{-- Cursos de esa categoría --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categoria->cursos as $curso)
                            <article class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group">
                                {{-- Imagen --}}
                                <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-200 relative overflow-hidden">
                                    @if($curso->imagen)
                                        <img src="{{ asset('storage/' . $curso->imagen) }}" alt="{{ $curso->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                    @endif
                                    @if($curso->destacado)
                                        <span class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-semibold flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Destacado
                                        </span>
                                    @endif
                                </div>

                                {{-- Información --}}
                                <div class="p-6">
                                    <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">{{ $curso->nombre }}</h3>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3 min-h-[4rem]">{{ $curso->descripcion_corta }}</p>

                                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $curso->duracion }}
                                        </div>
                                        <a href="{{ route('contacto') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900 transition flex items-center">
                                            Más info
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</section>

@endsection
