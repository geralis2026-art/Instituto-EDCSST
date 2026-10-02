@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-2 text-sm font-medium text-green-900 bg-green-50 border border-green-200 rounded-lg px-4 py-3']) }} role="status">
        <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ $status }}</span>
    </div>
@endif
