@php
    $logoPath = 'img/logo-edcsst.png';
@endphp

@if(file_exists(public_path($logoPath)))
    <img src="{{ asset($logoPath) }}" alt="Instituto EDCSST" {{ $attributes->merge(['class' => 'object-contain']) }}>
@else
    <div {{ $attributes->merge(['class' => 'bg-blue-700 text-white rounded-lg flex items-center justify-center font-bold']) }}>
        E
    </div>
@endif
