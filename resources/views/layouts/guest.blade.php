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
        <style>
            .login-wrap { display: flex; min-height: 100vh; }
            .login-image { display: none; position: relative; overflow: hidden; }
            .login-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: 40% center; }
            .login-image-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(30,58,138,0.80) 100%); }
            .login-image-content { position: relative; z-index: 10; display: flex; flex-direction: column; justify-content: space-between; padding: 3rem; height: 100%; }
            .login-form-col { display: flex; align-items: center; justify-content: center; padding: 2.5rem 1.5rem; width: 100%; background: #f8fafc; }
            .gold-bar { width: 3rem; height: 3px; border-radius: 9999px; background: linear-gradient(90deg, #F59E0B, #D4A017); margin-bottom: 1.5rem; }
            .gold-text { background: linear-gradient(90deg, #F59E0B, #D4A017); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
            .btn-login { width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.8rem 1.5rem; border-radius: 0.5rem; font-weight: 600; color: white; background: linear-gradient(135deg, #1e3a8a, #1d4ed8); border: none; cursor: pointer; font-size: 1rem; transition: opacity 0.2s; }
            .btn-login:hover { opacity: 0.92; }
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
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <x-application-logo style="width:2.8rem; height:2.8rem; color:#F59E0B;" class="fill-current" />
                        <div style="color:white; font-weight:700; line-height:1.2; font-size:1rem;">Instituto<br>EDCSST</div>
                    </div>

                    {{-- Texto central --}}
                    <div>
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
                    <a href="/" style="color:#93c5fd; text-decoration:none; font-size:0.875rem; display:inline-flex; align-items:center; gap:0.4rem; transition:color 0.2s;"
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
                    <div style="text-align:center; margin-bottom:2rem; display:block;" class="lg-hidden-logo">
                        <a href="/">
                            <x-application-logo style="width:4rem; height:4rem; color:#1e3a8a; margin:0 auto;" class="fill-current" />
                        </a>
                        <p style="color:#6b7280; font-size:0.875rem; margin-top:0.4rem;">Instituto EDCSST</p>
                    </div>

                    {{-- Card --}}
                    <div style="background:white; border-radius:1rem; box-shadow:0 4px 24px rgba(0,0,0,0.09); border:1px solid #f0f0f0; padding:2.25rem;">
                        <div style="margin-bottom:1.75rem;">
                            <h1 style="font-size:1.5rem; font-weight:700; color:#111827; margin:0 0 0.3rem;">Iniciar sesión</h1>
                            <p style="color:#6b7280; font-size:0.875rem; margin:0;">Ingresa tus credenciales para acceder al panel</p>
                            <div style="width:2.5rem; height:3px; border-radius:9999px; background:linear-gradient(90deg,#F59E0B,#D4A017); margin-top:0.85rem;"></div>
                        </div>

                        {{ $slot }}

                        <div style="margin-top:1.75rem; padding-top:1.25rem; border-top:1px solid #f3f4f6; text-align:center;">
                            <a href="/" style="color:#9ca3af; font-size:0.875rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem; transition:color 0.2s;"
                               onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#9ca3af'">
                                <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Volver al sitio
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
