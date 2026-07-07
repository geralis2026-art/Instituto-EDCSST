<?php
$logoPath = public_path('img/logo-edcsst.png');
$logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : null;

$config = config('certificado');
$correoValidacion = \App\Models\ConfiguracionSitio::obtener()->correo_contacto;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
        }

        .marco {
            border: 6px solid #c9a227;
            margin: 18px;
            padding: 0;
        }

        .header {
            background-color: #102a4c;
            background-image: repeating-linear-gradient(
                -45deg,
                #102a4c 0px,
                #102a4c 18px,
                #1c3f6e 18px,
                #1c3f6e 36px
            );
            color: #ffffff;
            padding: 14px 24px;
            overflow: hidden;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header .logo-cell {
            width: 90px;
        }

        .logo-box {
            background-color: #ffffff;
            border-radius: 6px;
            padding: 6px;
            width: 70px;
            height: 70px;
            text-align: center;
        }

        .logo-box img {
            width: 56px;
            height: 56px;
        }

        .header .titulo-cell {
            padding-left: 16px;
            vertical-align: middle;
        }

        .header .titulo-cell h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 3px;
            font-weight: bold;
        }

        .header .titulo-cell p {
            margin: 4px 0 0;
            font-size: 9px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header .titulo-cell .nit {
            margin-top: 6px;
            font-size: 9px;
        }

        .header .sello-cell {
            width: 110px;
            text-align: center;
            vertical-align: middle;
        }

        .sello {
            display: inline-block;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 4px solid #c9a227;
            background-color: #d4af37;
            color: #102a4c;
            text-align: center;
            line-height: 1.1;
            padding-top: 22px;
            font-weight: bold;
        }

        .sello .anio {
            font-size: 22px;
            display: block;
        }

        .sello .anio-label {
            font-size: 8px;
            letter-spacing: 1px;
            display: block;
        }

        .cuerpo {
            background-color: #ffffff;
            text-align: center;
            padding: 28px 60px 10px;
        }

        .cuerpo .hace-constar {
            font-size: 13px;
            letter-spacing: 4px;
            color: #6b7280;
            margin: 0 0 14px;
        }

        .cuerpo .nombre {
            font-size: 28px;
            font-weight: bold;
            color: #102a4c;
            margin: 0 0 4px;
            text-transform: uppercase;
        }

        .cuerpo .documento {
            font-size: 12px;
            color: #4b5563;
            margin: 0 0 14px;
        }

        .cuerpo .realizo {
            font-size: 11px;
            color: #4b5563;
            margin: 0 0 6px;
        }

        .cuerpo .curso {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1f2937;
            margin: 0 0 14px;
        }

        .cuerpo .resolucion {
            font-size: 10px;
            color: #6b7280;
            margin: 0 0 16px;
            padding: 0 40px;
        }

        .cuerpo .detalle {
            font-size: 11px;
            color: #374151;
            margin: 0 0 4px;
        }

        .cuerpo .ciudad {
            font-size: 11px;
            color: #374151;
            margin: 14px 0 4px;
        }

        .cuerpo .fechas {
            font-size: 11px;
            color: #374151;
            margin: 0 0 14px;
        }

        .cuerpo .codigo {
            font-size: 11px;
            font-weight: bold;
            color: #102a4c;
            margin: 0 0 18px;
            letter-spacing: 1px;
        }

        .pie {
            background-color: #ffffff;
            padding: 0 50px 22px;
        }

        .pie table {
            width: 100%;
            border-collapse: collapse;
        }

        .pie .firma-cell {
            width: 50%;
            text-align: center;
            font-size: 10px;
            color: #374151;
        }

        .pie .firma-cell .linea {
            border-top: 1px solid #9ca3af;
            width: 200px;
            margin: 0 auto 6px;
            padding-top: 6px;
        }

        .pie .firma-cell .firma-nombre {
            font-weight: bold;
            color: #102a4c;
        }

        .pie .legal-cell {
            width: 50%;
            text-align: left;
            font-size: 7.5px;
            color: #6b7280;
            line-height: 1.4;
            padding-left: 24px;
        }

        .pie .legal-cell .validacion {
            margin-top: 6px;
            font-weight: bold;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="marco">
        <div class="header">
            <table>
                <tr>
                    <td class="logo-cell">
                        <div class="logo-box">
                            @if($logoBase64)
                                <img src="{{ $logoBase64 }}" alt="EDCSST">
                            @endif
                        </div>
                    </td>
                    <td class="titulo-cell">
                        <h1>EDCSST</h1>
                        <p>Educación para el Desarrollo y la Calidad en Seguridad y Salud en el Trabajo S.A.S</p>
                        <p class="nit">NIT: 902045874-6</p>
                    </td>
                    <td class="sello-cell">
                        <div class="sello">
                            <span class="anio">{{ $certificado->fecha_emision->year }}</span>
                            <span class="anio-label">CERTIFICADO</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="cuerpo">
            <p class="hace-constar">HACE CONSTAR QUE</p>
            <p class="nombre">{{ $certificado->capacitado->nombre_completo }}</p>
            <p class="documento">{{ $certificado->capacitado->tipoDocumentoAbreviado() }} {{ $certificado->capacitado->documento }}</p>
            <p class="realizo">Realizó y aprobó el curso de:</p>
            <p class="curso">{{ $certificado->curso->nombre }}</p>
            <p class="resolucion">{{ $config['texto_resolucion'] }}</p>

            <p class="detalle">
                MODALIDAD: {{ $certificado->modalidad ? ucfirst($certificado->modalidad) : 'No especificada' }}
                &nbsp;/&nbsp;
                Duración: {{ $certificado->intensidad_horaria }} horas
            </p>

            <p class="ciudad">Expedido en {{ $config['ciudad_expedicion'] }}</p>
            <p class="fechas">
                Fecha de emisión: {{ $certificado->fecha_emision->format('d/m/Y') }}
                &nbsp;|&nbsp;
                Fecha de vencimiento: {{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? 'No aplica' }}
            </p>
            <p class="codigo">CÓDIGO DE VERIFICACIÓN: {{ $certificado->codigo_unico }}</p>
        </div>

        <div class="pie">
            <table>
                <tr>
                    <td class="firma-cell">
                        <div class="linea">
                            <span class="firma-nombre">{{ $config['firmante_nombre'] }}</span><br>
                            {{ $config['firmante_cargo'] }}<br>
                            @foreach($config['firmante_detalle'] as $linea)
                                {{ $linea }}<br>
                            @endforeach
                        </div>
                    </td>
                    <td class="legal-cell">
                        {{ $config['texto_legal'] }}
                        @if($correoValidacion)
                            <p class="validacion">Verifique la autenticidad de este certificado escribiendo a {{ $correoValidacion }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
