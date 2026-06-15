@props(['paginator'])

@if($paginator->lastPage() > 1)
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            {{ $paginator->appends(request()->query())->links() }}
        </div>

        <form method="GET" class="flex items-center gap-2 text-sm text-gray-600">
            @foreach(request()->except('page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <label for="ir-a-pagina-{{ $paginator->getPageName() }}">Ir a la página:</label>
            <select id="ir-a-pagina-{{ $paginator->getPageName() }}"
                    name="{{ $paginator->getPageName() }}"
                    onchange="this.form.submit()"
                    class="rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                @for($i = 1; $i <= $paginator->lastPage(); $i++)
                    <option value="{{ $i }}" @selected($i === $paginator->currentPage())>{{ $i }}</option>
                @endfor
            </select>
        </form>
    </div>
@endif
