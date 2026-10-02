{{--
    Chips de filtro por estado, reutilizables en los listados del panel.
    Recibe: $ruta (nombre de la ruta del listado), $chips (valor => etiqueta, '' = sin filtro),
            $actual (valor activo o null) y opcionalmente $titulo.
    Conserva el resto de parámetros de la URL (búsqueda, curso, instructor...) y reinicia la página.
--}}
@php
    $urlChip = fn (string $valor) => route($ruta, array_filter(
        [...request()->except('page', 'estado', 'vence'), 'estado' => $valor ?: null]
    ));
@endphp
<nav aria-label="Filtrar por {{ strtolower($titulo ?? 'estado') }}" class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-100">
    <span class="text-sm font-semibold text-slate-600 mr-1">{{ $titulo ?? 'Estado' }}:</span>
    @foreach($chips as $valor => $etiqueta)
        <a href="{{ $urlChip((string) $valor) }}" class="filtro-chip"
           @if((string) ($actual ?? '') === (string) $valor) aria-current="true" @endif>{{ $etiqueta }}</a>
    @endforeach
</nav>
