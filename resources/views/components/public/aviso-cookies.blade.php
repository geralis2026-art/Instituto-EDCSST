{{--
    Aviso de cookies. El sitio solo usa cookies técnicas necesarias (sesión y seguridad); lo único opcional
    es Google reCAPTCHA en el formulario de contacto, que no se carga hasta que la persona lo permita.
    La elección se guarda en el almacenamiento local del navegador (clave "edcsst_cookies"), no en una cookie.
    Expone window.edcsstCookies = { get(), set(valor), abrir() } y emite el evento "edcsst:cookies".
--}}
<section id="aviso-cookies" role="region" aria-labelledby="aviso-cookies-titulo" hidden
         class="fixed inset-x-0 bottom-0 z-[60] bg-white border-t-4 border-amber-400 shadow-[0_-8px_30px_rgba(11,30,74,0.18)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col lg:flex-row lg:items-center gap-5">
        <div class="flex-1 text-[15px] leading-relaxed text-slate-700">
            <h2 id="aviso-cookies-titulo" class="font-titulo font-semibold text-slate-900 text-base">Tu privacidad en este sitio</h2>
            <p class="mt-1 max-w-4xl">
                Usamos solo cookies técnicas necesarias (sesión y seguridad) para que el sitio funcione.
                El formulario de contacto utiliza Google reCAPTCHA, que únicamente se activa si tú lo permites.
                No usamos cookies de publicidad ni de seguimiento.
                <a href="{{ route('politica.cookies') }}" class="font-semibold text-marca-navy underline underline-offset-2 hover:text-marca-navy-claro">Política de cookies</a>
                ·
                <a href="{{ route('politica.privacidad') }}" class="font-semibold text-marca-navy underline underline-offset-2 hover:text-marca-navy-claro">Política de privacidad</a>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
            <button type="button" data-cookies="necesarias"
                    class="inline-flex items-center justify-center min-h-[48px] px-6 rounded-lg border-2 border-marca-navy text-marca-navy font-semibold hover:bg-slate-50 transition-colors">
                Solo las necesarias
            </button>
            <button type="button" data-cookies="todas"
                    class="inline-flex items-center justify-center min-h-[48px] px-6 rounded-lg border-2 border-marca-navy bg-marca-navy text-white font-semibold hover:bg-marca-navy-claro hover:border-marca-navy-claro transition-colors">
                Aceptar todas
            </button>
        </div>
    </div>
</section>

<script nonce="{{ $cspNonce }}">
    (function () {
        const CLAVE = 'edcsst_cookies';
        const aviso = document.getElementById('aviso-cookies');
        const leer = () => { try { return localStorage.getItem(CLAVE); } catch (e) { return null; } };
        const guardar = (v) => { try { localStorage.setItem(CLAVE, v); } catch (e) { /* sin almacenamiento: se volverá a preguntar */ } };

        // Mientras el aviso está visible se reserva su alto abajo, para que no tape el contenido ni el foco.
        const ajustarEspacio = () => { document.body.style.paddingBottom = aviso.hidden ? '' : aviso.offsetHeight + 'px'; };
        const mostrar = () => { aviso.hidden = false; ajustarEspacio(); };
        const ocultar = () => { aviso.hidden = true; ajustarEspacio(); };

        window.edcsstCookies = {
            get: leer,
            abrir: mostrar,
            set(valor) {
                guardar(valor);
                ocultar();
                document.dispatchEvent(new CustomEvent('edcsst:cookies', { detail: valor }));
            },
        };

        aviso.querySelectorAll('[data-cookies]').forEach((b) => b.addEventListener('click', () => window.edcsstCookies.set(b.dataset.cookies)));
        document.querySelectorAll('[data-abrir-cookies]').forEach((b) => b.addEventListener('click', mostrar));
        window.addEventListener('resize', ajustarEspacio);

        if (leer() === null) mostrar();
    })();
</script>
