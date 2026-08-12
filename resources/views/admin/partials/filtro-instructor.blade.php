{{-- Selector "Ver: Todos / Edna / Mauricio" — solo visible para admin. --}}
@if(auth()->user()->isAdmin() && $gestores->count() > 1)
    <select name="instructor" onchange="this.form.submit()"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Ver: Todos</option>
        @foreach($gestores as $gestor)
            <option value="{{ $gestor->id }}" @selected($instructorId == $gestor->id)>Ver: {{ $gestor->name }}</option>
        @endforeach
    </select>
@endif
