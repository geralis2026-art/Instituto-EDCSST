<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultaBuscarRequest;
use App\Models\Capacitado;
use App\Models\Certificado;
use App\Services\CertificadoPdfService;
use App\Services\MergePdfService;
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
    /** Muestra el formulario de búsqueda. */
    public function index()
    {
        return view('public.consulta');
    }

    /**
     * Procesa la búsqueda de certificados.
     * El usuario puede buscar por documento o por código único.
     */
    public function buscar(ConsultaBuscarRequest $request)
    {
        $datos        = $request->validated();
        $valor        = $datos['valor'];
        $certificados = collect();
        $capacitado   = null;
        $mensajeError = null;

        if ($datos['tipo_busqueda'] === 'documento') {
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
            $certificado = Certificado::porCodigo($valor);

            if ($certificado) {
                $capacitado   = $certificado->capacitado;
                $certificados = collect([$certificado->load('curso.categoria')]);
            } else {
                $mensajeError = 'No encontramos certificados con esos datos. Verifica la información o contacta al instituto.';
            }
        }

        $urlsDescarga = $certificados
            ->filter(fn ($c) => !$c->isVencido())
            ->mapWithKeys(fn ($c) => [
                $c->id => URL::temporarySignedRoute('consulta.descargar', now()->addMinutes(30), $c),
            ]);

        $urlDescargarTodos = null;
        $urlDescargarSeleccionados = null;

        if ($capacitado && $urlsDescarga->count() >= 2) {
            $urlDescargarTodos = URL::temporarySignedRoute('consulta.descargarTodos', now()->addMinutes(30), $capacitado);
            $urlDescargarSeleccionados = URL::temporarySignedRoute('consulta.descargarSeleccionados', now()->addMinutes(30), $capacitado);
        }

        return view('public.consulta', compact('certificados', 'capacitado', 'mensajeError', 'urlsDescarga', 'urlDescargarTodos', 'urlDescargarSeleccionados'))
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
    public function descargar(Certificado $certificado, CertificadoPdfService $pdfService)
    {
        if (!$certificado->activo || !$certificado->archivo_pdf) {
            abort(404, 'Este certificado no está disponible para descarga.');
        }

        if ($certificado->isVencido()) {
            abort(403, 'Este certificado ha vencido y no está disponible para descarga.');
        }

        $path = $certificado->archivo_pdf;

        if (!str_starts_with($path, 'certificados/') || str_contains($path, '..')) {
            abort(404, 'El archivo del certificado no se encuentra. Por favor contacta al instituto.');
        }

        // Autorregeneración: si el PDF no quedó guardado en disco (fallo previo
        // de escritura), se genera de nuevo aquí mismo en vez de fallar con 404.
        // generarYGuardar() siempre escribe en "certificados/{codigo}.pdf", que
        // puede diferir de $path si el PDF original era una carga manual con
        // nombre aleatorio — por eso se debe releer y persistir la ruta nueva.
        if (!Storage::disk('certificados')->exists($path)) {
            try {
                $path = $pdfService->generarYGuardar($certificado);
            } catch (\RuntimeException $e) {
                report($e);
                abort(404, 'El archivo del certificado no se encuentra. Por favor contacta al instituto.');
            }

            $certificado->archivo_pdf = $path;
            $certificado->saveQuietly();
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

    /**
     * Descarga, en un solo PDF, todos los certificados activos y vigentes
     * del capacitado (acceso solo vía URL firmada temporal generada en buscar()).
     */
    public function descargarTodos(Capacitado $capacitado, MergePdfService $merge, CertificadoPdfService $pdfService)
    {
        $certificados = $capacitado->certificados()
            ->where('activo', true)
            ->get()
            ->filter(fn ($c) => !$c->isVencido() && $c->archivo_pdf)
            ->filter(fn ($c) => str_starts_with($c->archivo_pdf, 'certificados/') && !str_contains($c->archivo_pdf, '..'))
            ->filter(fn ($c) => $this->asegurarPdfExiste($c, $pdfService));

        if ($certificados->isEmpty()) {
            abort(404, 'No hay certificados disponibles para descargar.');
        }

        $rutas = $certificados
            ->map(fn ($c) => Storage::disk('certificados')->path($c->archivo_pdf))
            ->values()
            ->all();

        $pdf = $merge->fusionar($rutas);

        $nombreArchivo = sprintf('Certificados_%s.pdf', Str::slug($capacitado->nombre_completo, '_'));

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    /**
     * Descarga, en un solo PDF, los certificados seleccionados por el usuario
     * (acceso solo vía URL firmada temporal generada en buscar()). La firma
     * protege la URL (y por tanto al capacitado); los IDs seleccionados llegan
     * en el cuerpo del POST y se filtran para que solo puedan ser certificados
     * de ese mismo capacitado, activos y vigentes.
     */
    public function descargarSeleccionados(Request $request, Capacitado $capacitado, MergePdfService $merge, CertificadoPdfService $pdfService)
    {
        $ids = collect($request->input('certificado_ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('error', 'Selecciona al menos un certificado para descargar.');
        }

        $certificados = $capacitado->certificados()
            ->whereIn('id', $ids)
            ->where('activo', true)
            ->get()
            ->filter(fn ($c) => !$c->isVencido() && $c->archivo_pdf)
            ->filter(fn ($c) => str_starts_with($c->archivo_pdf, 'certificados/') && !str_contains($c->archivo_pdf, '..'))
            ->filter(fn ($c) => $this->asegurarPdfExiste($c, $pdfService));

        if ($certificados->isEmpty()) {
            abort(404, 'No hay certificados disponibles para descargar.');
        }

        $rutas = $certificados
            ->map(fn ($c) => Storage::disk('certificados')->path($c->archivo_pdf))
            ->values()
            ->all();

        $pdf = $merge->fusionar($rutas);

        $nombreArchivo = sprintf('Certificados_seleccionados_%s.pdf', Str::slug($capacitado->nombre_completo, '_'));

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    /**
     * Verifica que el PDF del certificado exista en disco; si no, intenta
     * regenerarlo (recupera archivos que quedaron con la ruta guardada en BD
     * pero sin escribirse realmente, ver CertificadoPdfService::generarYGuardar).
     */
    private function asegurarPdfExiste(Certificado $certificado, CertificadoPdfService $pdfService): bool
    {
        if (Storage::disk('certificados')->exists($certificado->archivo_pdf)) {
            return true;
        }

        // generarYGuardar() siempre escribe en "certificados/{codigo}.pdf", que
        // puede diferir de archivo_pdf si el original era una carga manual con
        // nombre aleatorio — hay que persistir la ruta nueva, si no, el merge
        // más abajo seguiría intentando leer la ruta vieja (inexistente).
        try {
            $certificado->archivo_pdf = $pdfService->generarYGuardar($certificado);
            $certificado->saveQuietly();

            return true;
        } catch (\RuntimeException $e) {
            report($e);

            return false;
        }
    }
}
