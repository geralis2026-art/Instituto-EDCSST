<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-admin bg-red-600 border-red-600 text-white hover:bg-red-700 hover:border-red-700']) }}>
    {{ $slot }}
</button>
