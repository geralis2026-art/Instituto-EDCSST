{{--
    Casilla de autorización para el tratamiento de datos personales (Ley 1581 de 2012).
    Debe ser un acto afirmativo: nunca va premarcada. Parámetro: $finalidad (frase que completa
    "con la finalidad de ...").
--}}
<div>
    <div class="flex items-start gap-3">
        <input type="checkbox" id="acepta_politica" name="acepta_politica" value="1" required
               {{ old('acepta_politica') ? 'checked' : '' }}
               @error('acepta_politica') aria-invalid="true" aria-describedby="acepta_politica-error" @enderror
               class="mt-1 h-5 w-5 shrink-0 rounded border-slate-400 text-marca-navy focus:ring-2 focus:ring-amber-400">
        <label for="acepta_politica" class="text-[15px] leading-relaxed text-slate-700">
            He leído la
            <a href="{{ route('politica.privacidad') }}" target="_blank" rel="noopener" class="font-semibold text-marca-navy underline underline-offset-2 hover:text-marca-navy-claro">Política de Tratamiento de Datos Personales<span class="sr-only"> (abre en una pestaña nueva)</span></a>
            y autorizo de forma previa, expresa e informada a {{ config('politicas.nombre_corto') }} para tratar mis datos personales
            con la finalidad de {{ $finalidad }}.
            Sé que puedo conocer, actualizar, rectificar y suprimir mis datos, y revocar esta autorización cuando quiera.
            <span class="text-red-600" aria-hidden="true">*</span>
        </label>
    </div>
    @error('acepta_politica')
        <p id="acepta_politica-error" class="text-red-700 text-sm mt-1.5 ml-8" role="alert">{{ $message }}</p>
    @enderror
</div>
