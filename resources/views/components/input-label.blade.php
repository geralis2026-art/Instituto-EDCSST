@props(['value'])

<label {{ $attributes->merge(['class' => 'etiqueta-admin']) }}>
    {{ $value ?? $slot }}
</label>
