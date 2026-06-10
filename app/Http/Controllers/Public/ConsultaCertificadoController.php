<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Consulta pública de certificados por documento o código único,
 * con descarga de PDF mediante enlaces firmados temporales
 * (30 minutos). Solo certificados activos y no vencidos son
 * descargables.
 */
class ConsultaCertificadoController extends Controller
{
    /**
     * Muestra el formulario de búsqueda.
     */
    public function index()
    {
        return view('public.consulta');
    }

    /**
     * Procesa la búsqueda de certificados.
     * El usuario puede buscar por documento o por código único.
     */
    public function buscar(Request $request)
    {
        $datos = $request->validate([
            'tipo_busqueda' => 'required|in:documento,codigo',
            'valor' => 'required|string|max:50',
        ], [
            'valor.required' => 'Por favor ingresa un valor de búsqueda.',
        ]);

        $valor = trim($datos['valor']);
        $certificados = collect();
        $capacitado = null;
        $mensajeError = null;

        if ($datos['tipo_busqueda'] === 'documento') {
            // Buscar por documento del capacitado
            $capacitado = Capacitado::porDocumento($valor);

            if ($capacitado) {
                $certificados = $capacitado->certificados()
                    ->with('curso.categoria')
                    ->where('activo', true)
                    ->orderBy('fecha_emision', 'desc')
                    ->get();
            }

            if (!$capacitado || $certificados->isEmpty()) {
                $mensajeError = 'No encontramos certificados con esos datos. Verifica la información o contacta al instituto.';
            }
        } else {
            // Buscar por código único del certificado
            $certificado = Certificado::porCodigo($valor);

            if ($certificado) {
                $capacitado = $certificado->capacitado;
                $certificados = collect([$certificado->load('curso.categoria')]);
            } else {
                $mensajeError = 'No encontramos certificados con esos datos. Verifica la información o contacta al instituto.';
            }
        }

        $urlsDescarga = $certificados
            ->filter(fn($c) => !$c->isVencido())
            ->mapWithKeys(fn($c) => [
                $c->id => URL::temporarySignedRoute('consulta.descargar', now()->addMinutes(30), $c)
            ]);

        return view('public.consulta', compact('certificados', 'capacitado', 'mensajeError', 'urlsDescarga'))
            ->with('busquedaRealizada', true)
            ->with('tipoBusqueda', $datos['tipo_busqueda'])
            ->with('valorBuscado', $valor);
    }

    /**
     * Descarga el PDF del certificado (acceso solo vía URL firmada
     * temporal generada en buscar()). Verifica nuevamente que el
     * certificado esté activo y vigente, y valida la ruta del
     * archivo para evitar path traversal.
     */
    public function descargar(Certificado $certificado)
    {
        if (!$certificado->activo || !$certificado->archivo_pdf) {
            abort(404, 'Este certificado no está disponible para descarga.');
        }

        if ($certificado->isVencido()) {
            abort(403, 'Este certificado ha vencido y no está disponible para descarga.');
        }

        $path = $certificado->archivo_pdf;

        if (!str_starts_with($path, 'certificados/') || str_contains($path, '..') || !Storage::disk('certificados')->exists($path)) {
            abort(404, 'El archivo del certificado no se encuentra. Por favor contacta al instituto.');
        }

        $nombreArchivo = sprintf(
            'Certificado_%s_%s.pdf',
            Str::slug($certificado->capacitado->nombre_completo, '_'),
            $certificado->codigo_unico
        );

        return response()->download(
            Storage::disk('certificados')->path($path),
            $nombreArchivo,
            ['Content-Type' => 'application/pdf']
        );
    }
}
