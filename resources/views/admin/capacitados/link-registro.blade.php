@extends('layouts.admin')

@section('titulo', 'Link de Registro')
@section('titulo_topbar', 'Link de Registro de Capacitados')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Link generado exitosamente</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Este link expira a las <span class="font-semibold text-amber-600">{{ $expira }}</span>
                    (20 minutos). Compártelo con los capacitados para que se registren.
                </p>
            </div>
        </div>

        {{-- Link para copiar --}}
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Link de registro</label>
            <div class="flex gap-2">
                <input id="link-registro"
                       type="text"
                       value="{{ $url }}"
                       readonly
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400">
                <button onclick="copiarLink()"
                        id="btn-copiar"
                        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2 whitespace-nowrap">
                    <svg id="icon-copiar" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span id="texto-copiar">Copiar</span>
                </button>
            </div>
        </div>

        {{-- Instrucciones --}}
        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800 space-y-1">
            <p class="font-semibold">¿Cómo usar este link?</p>
            <ul class="list-disc list-inside space-y-1 text-blue-700">
                <li>Copia el link y compártelo por WhatsApp, correo o proyéctalo en pantalla.</li>
                <li>Cada capacitado abre el link en su celular o computador y llena sus datos.</li>
                <li>Pueden seleccionar uno o varios cursos en el mismo formulario.</li>
                <li>Las solicitudes quedan pendientes y puedes generar los certificados desde
                    <a href="{{ route('admin.certificados.masivos') }}" class="underline font-medium">Certificados masivos</a>.</li>
            </ul>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="flex justify-between">
        <a href="{{ route('admin.capacitados.index') }}"
           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            ← Volver a capacitados
        </a>
        <a href="{{ route('admin.capacitados.link-registro') }}"
           class="px-4 py-2 text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
            Generar nuevo link
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
function copiarLink() {
    const input = document.getElementById('link-registro');
    const btn   = document.getElementById('btn-copiar');
    const texto = document.getElementById('texto-copiar');

    navigator.clipboard.writeText(input.value).then(() => {
        texto.textContent = '¡Copiado!';
        btn.classList.replace('bg-amber-500', 'bg-green-500');
        btn.classList.replace('hover:bg-amber-600', 'hover:bg-green-600');

        setTimeout(() => {
            texto.textContent = 'Copiar';
            btn.classList.replace('bg-green-500', 'bg-amber-500');
            btn.classList.replace('hover:bg-green-600', 'hover:bg-amber-600');
        }, 2500);
    }).catch(() => {
        input.select();
        document.execCommand('copy');
    });
}
</script>
@endpush
