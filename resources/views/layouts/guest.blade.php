<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
        <title>{{ config('app.name', 'Laravel') }} — Acceso</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <x-app-assets />
        <style nonce="{{ $cspNonce }}">
            /* ── Layout ── */
            .login-wrap { display: flex; min-height: 100vh; }
            .login-image { display: none; position: relative; overflow: hidden; }
            .login-image-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(30,58,138,0.80) 100%); }
            .login-image-content { position: relative; z-index: 10; display: flex; flex-direction: column; justify-content: space-between; padding: 3rem; height: 100%; }
            .login-form-col { display: flex; align-items: center; justify-content: center; padding: 2.5rem 1.5rem; width: 100%; background: #f8fafc; }

            /* ── Colores ── */
            .gold-text { background: linear-gradient(90deg, #F59E0B, #D4A017); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
            .btn-login { width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; border-radius: 0.5rem; font-weight: 600; color: white; background: linear-gradient(135deg, #1e3a8a, #1d4ed8); border: none; cursor: pointer; font-size: 1rem; transition: transform 0.18s, box-shadow 0.18s; }
            .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(29,78,216,0.45); }
            .btn-login:active { transform: translateY(0); }

            /* ── Animaciones keyframes ── */
            @keyframes kenBurns {
                0%   { transform: scale(1)    translate(0, 0); }
                100% { transform: scale(1.08) translate(-2%, -1%); }
            }
            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-28px); }
                to   { opacity: 1; transform: translateX(0); }
            }
            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            @keyframes goldExpand {
                from { width: 0; }
                to   { width: 3rem; }
            }
            @keyframes logoFloat {
                0%, 100% { transform: translateY(0); }
                50%      { transform: translateY(-6px); }
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to   { opacity: 1; }
            }

            /* ── Aplicar animaciones ── */
            .login-image > img {
                position: absolute; inset: 0; width: 100%; height: 100%;
                object-fit: cover; object-position: 40% center;
                animation: kenBurns 12s ease-out forwards;
            }
            .img-logo-top  { animation: slideInLeft 0.6s ease both; }
            .img-text-mid  { animation: slideInLeft 0.6s ease 0.18s both; }
            .img-link-bot  { animation: slideInLeft 0.6s ease 0.32s both; }
            .gold-bar {
                height: 3px; border-radius: 9999px;
                background: linear-gradient(90deg, #F59E0B, #D4A017);
                margin-bottom: 1.5rem; width: 0;
                animation: goldExpand 0.7s ease 0.45s forwards;
            }
            .login-card    { animation: fadeUp 0.65s ease 0.1s both; }
            .logo-float    { animation: logoFloat 3.5s ease-in-out infinite; }
            .logo-mobile-wrap { animation: fadeIn 0.5s ease both; }

            @media (min-width: 1024px) {
                .login-image { display: flex; flex-direction: column; width: 50%; }
                .login-form-col { width: 50%; }
            }
        </style>
    </head>
    <body style="font-family: 'Figtree', sans-serif; margin: 0;">
        <div class="login-wrap">

            {{-- Panel imagen --}}
            <div class="login-image">
                <img src="{{ asset('images/capacitacion-grupal-docencia.jpg') }}" alt="Instituto EDCSST">
                <div class="login-image-overlay"></div>
                <div class="login-image-content">
                    {{-- Logo --}}
                    <a href="/" class="img-logo-top" style="display:flex; align-items:center; gap:1rem; text-decoration:none;">
                        <img src="{{ asset('img/logo-edcsst.png') }}" alt="Logo EDCSST"
                             style="width:4rem; height:4rem; object-fit:contain; flex-shrink:0;">
                        <span style="color:white; font-weight:700; line-height:1.3; font-size:1.25rem;">Instituto<br>EDCSST</span>
                    </a>

                    {{-- Texto central --}}
                    <div class="img-text-mid">
                        <div class="gold-bar"></div>
                        <h2 style="color:white; font-size:1.75rem; font-weight:700; line-height:1.3; margin:0 0 1rem;">
                            Panel administrativo del<br>
                            <span class="gold-text">Instituto EDCSST</span>
                        </h2>
                        <p style="color:#bfdbfe; line-height:1.6; margin:0;">
                            Gestión de capacitados, certificados y cursos.<br>
                            Solo personal autorizado.
                        </p>
                    </div>

                    {{-- Volver al sitio --}}
                    <a href="/" class="img-link-bot" style="color:#93c5fd; text-decoration:none; font-size:0.875rem; display:inline-flex; align-items:center; gap:0.4rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#F59E0B'" onmouseout="this.style.color='#93c5fd'">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al sitio público
                    </a>
                </div>
            </div>

            {{-- Panel formulario --}}
            <div class="login-form-col">
                <div style="width:100%; max-width:26rem;">

                    {{-- Logo móvil --}}
                    <div class="logo-mobile-wrap" style="text-align:center; margin-bottom:2rem;">
                        <a href="/" style="display:inline-block;">
                            <img src="{{ asset('img/logo-edcsst.png') }}" alt="Logo EDCSST"
                                 class="logo-float"
                                 style="width:6rem; height:6rem; object-fit:contain; margin:0 auto;">
                        </a>
                        <p style="color:#6b7280; font-size:0.875rem; margin-top:0.4rem;">Instituto EDCSST</p>
                    </div>

                    {{-- Card --}}
                    <div class="login-card" style="background:white; border-radius:1rem; box-shadow:0 4px 24px rgba(0,0,0,0.09); border:1px solid #f0f0f0; padding:2.25rem;">
                        <div style="margin-bottom:1.75rem;">
                            <h1 style="font-size:1.5rem; font-weight:700; color:#111827; margin:0 0 0.3rem;">Iniciar sesión</h1>
                            <p style="color:#6b7280; font-size:0.875rem; margin:0;">Ingresa tus credenciales para acceder al panel</p>
                            <div style="width:2.5rem; height:3px; border-radius:9999px; background:linear-gradient(90deg,#F59E0B,#D4A017); margin-top:0.85rem;"></div>
                        </div>

                        {{ $slot }}

                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
