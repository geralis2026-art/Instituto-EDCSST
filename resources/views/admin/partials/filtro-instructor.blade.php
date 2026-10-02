{{-- Selector "Ver: Todos / Edna / Mauricio" — solo visible para admin. --}}
@if(auth()->user()->isAdmin() && $gestores->count() > 1)
    <select name="instructor" data-auto-submit aria-label="Filtrar por instructor"
            class="campo-admin sm:w-56">
        <option value="">Ver: Todos</option>
        @foreach($gestores as $gestor)
            <option value="{{ $gestor->id }}" @selected($instructorId == $gestor->id)>Ver: {{ $gestor->name }}</option>
        @endforeach
    </select>
@endif
