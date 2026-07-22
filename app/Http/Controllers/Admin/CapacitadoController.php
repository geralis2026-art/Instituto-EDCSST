<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapacitadoImportConfirmarRequest;
use App\Http\Requests\CapacitadoImportRequest;
use App\Http\Requests\CapacitadoRequest;
use App\Models\Capacitado;
use App\Services\CertificadoPdfService;
use App\Services\ImportacionCapacitadosService;
use App\Services\MergePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Gestión de capacitados (personas que reciben certificados).
 * Lectura disponible para admin y capacitador; crear, editar y
 * eliminar solo para admin (ver routes/web.php).
 */
class CapacitadoController extends Controller
{
    /**
     * Listado paginado de capacitados con búsqueda y filtros.
     */
    public function index(Request $request)
    {
        $busqueda = substr(trim((string) $request->query('busqueda', '')), 0, 100);
        
        $capacitados = Capacitado::query()
            ->when($busqueda, fn ($query) =>
                $query->where('nombre_completo', 'like', "%{$busqueda}%")
                      ->orWhere('documento', 'like', "%{$busqueda}%")
                      ->orWhere('correo', 'like', "%{$busqueda}%")
            )
            ->orderBy('nombre_completo')
            ->paginate(15)
            ->withQueryString();

        return view('admin.capacitados.index', compact('capacitados', 'busqueda'));
    }

    /**
     * Mostrar formulario para crear nuevo capacitado.
     */
    public function create()
    {
        return view('admin.capacitados.create');
    }

    /**
     * Guardar nuevo capacitado en base de datos.
     */
    public function store(CapacitadoRequest $request)
    {
        $capacitado = Capacitado::create($request->validated());

        return redirect()
            ->route('admin.capacitados.show', $capacitado)
            ->with('success', 'Capacitado registrado correctamente.');
    }

    /**
     * Mostrar detalle de un capacitado específico.
     */
    public function show(Capacitado $capacitado)
    {
        $certificados = $capacitado->certificados()
            ->with(['curso.categoria'])
            ->where('activo', true)
            ->orderBy('fecha_emision', 'desc')
            ->get();

        return view('admin.capacitados.show', compact('capacitado', 'certificados'));
    }

    /**
     * Descarga, en un solo PDF, todos los certificados activos y vigentes del capacitado.
     * Si solo hay uno, se descarga directamente sin fusionar.
     */
    public function descargarCertificados(Capacitado $capacitado, MergePdfService $merge)
    {
        $certificados = $capacitado->certificados()
            ->where('activo', true)
            ->get()
            ->filter(fn ($c) => !$c->isVencido() && $c->archivo_pdf && Storage::disk('certificados')->exists($c->archivo_pdf));

        if ($certificados->isEmpty()) {
            return back()->with('error', 'Este capacitado no tiene certificados con PDF disponibles.');
        }

        $nombreArchivo = sprintf('Certificados_%s.pdf', Str::slug($capacitado->nombre_completo, '_'));

        if ($certificados->count() === 1) {
            return response()->download(
                Storage::disk('certificados')->path($certificados->first()->archivo_pdf),
                $nombreArchivo,
                ['Content-Type' => 'application/pdf']
            );
        }

        $rutas = $certificados->map(fn ($c) => Storage::disk('certificados')->path($c->archivo_pdf))->values()->all();

        $pdf = $merge->fusionar($rutas);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    /**
     * Mostrar formulario para editar capacitado.
     */
    public function edit(Capacitado $capacitado)
    {
        return view('admin.capacitados.edit', compact('capacitado'));
    }

    /**
     * Actualizar capacitado en base de datos. Si cambian los datos que se
     * imprimen en el certificado (nombre, tipo o número de documento), se
     * regeneran automáticamente los PDFs de sus certificados activos.
     */
    public function update(CapacitadoRequest $request, Capacitado $capacitado, CertificadoPdfService $pdfService)
    {
        $capacitado->update($request->validated());

        $mensaje = 'Capacitado actualizado correctamente.';
        $tipoMensaje = 'success';

        if ($capacitado->wasChanged(['nombre_completo', 'tipo_documento', 'documento'])) {
            [$errores, $omitidos] = $this->regenerarCertificadosDe($capacitado, $pdfService);

            if ($errores === 0 && $omitidos === 0) {
                $mensaje = 'Capacitado actualizado y certificados regenerados correctamente.';
            } elseif ($errores === 0) {
                $mensaje = "Capacitado actualizado y certificados regenerados. {$omitidos} certificado(s) con PDF cargado manualmente no se tocaron — actualízalos a mano si corresponde.";
                $tipoMensaje = 'success';
            } else {
                $mensaje = "Capacitado actualizado, pero {$errores} certificado(s) no se pudieron regenerar. Usa \"Regenerar PDF\" en cada uno.";
                $tipoMensaje = 'error';
            }
        }

        return redirect()
            ->route('admin.capacitados.show', $capacitado)
            ->with($tipoMensaje, $mensaje);
    }

    /**
     * Regenera el PDF de cada certificado activo del capacitado (genera primero
     * y solo borra el archivo anterior si la generación fue exitosa). Omite los
     * certificados con un PDF cargado manualmente (nombre de archivo distinto
     * al generado automáticamente) para no reemplazar un documento personalizado
     * con la plantilla estándar.
     * Devuelve [fallos, omitidos].
     */
    private function regenerarCertificadosDe(Capacitado $capacitado, CertificadoPdfService $pdfService): array
    {
        $fallos = 0;
        $omitidos = 0;

        $certificados = $capacitado->certificados()
            ->where('activo', true)
            ->with('curso.categoria')
            ->get();

        foreach ($certificados as $certificado) {
            $certificado->setRelation('capacitado', $capacitado);

            if ($certificado->archivo_pdf !== "certificados/{$certificado->codigo_unico}.pdf") {
                $omitidos++;
                continue;
            }

            try {
                $nuevoPdf = $pdfService->generarYGuardar($certificado);
            } catch (\RuntimeException $e) {
                report($e);
                $fallos++;
                continue;
            }

            if ($certificado->archivo_pdf) {
                Storage::disk('certificados')->delete($certificado->archivo_pdf);
            }

            $certificado->archivo_pdf = $nuevoPdf;
            $certificado->saveQuietly();
        }

        return [$fallos, $omitidos];
    }

    /**
     * Eliminar capacitado de la base de datos.
     */
    public function destroy(Capacitado $capacitado)
    {
        if ($capacitado->certificados()->exists()) {
            return back()
                ->with('error', 'No se puede eliminar este capacitado porque tiene certificados asociados.');
        }

        $capacitado->delete();

        return redirect()
            ->route('admin.capacitados.index')
            ->with('success', 'Capacitado eliminado correctamente.');
    }

    /**
     * Búsqueda AJAX de capacitados por cédula o nombre (para el formulario de certificados).
     */
    public function buscar(Request $request)
    {
        $q = substr(trim((string) $request->query('q', '')), 0, 100);

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $resultados = Capacitado::where('documento', 'like', "%{$q}%")
            ->orWhere('nombre_completo', 'like', "%{$q}%")
            ->orderBy('nombre_completo')
            ->limit(10)
            ->get(['id', 'nombre_completo', 'tipo_documento', 'documento']);

        return response()->json($resultados);
    }

    /**
     * Genera un link temporal de 20 minutos para que los capacitados se auto-registren.
     * El token se guarda en caché; el link se comparte por WhatsApp o correo.
     */
    public function generarLinkRegistro()
    {
        $token = Str::random(40);
        Cache::put("reg:{$token}", true, now()->addMinutes(20));

        $url    = route('registro.form', ['token' => $token]);
        $expira = now()->addMinutes(20)->format('H:i');

        return view('admin.capacitados.link-registro', compact('url', 'expira'));
    }

    /**
     * Descarga la plantilla Excel para la importación masiva de capacitados.
     */
    public function descargarPlantilla(ImportacionCapacitadosService $servicio)
    {
        $spreadsheet = $servicio->generarPlantilla();

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'plantilla-capacitados.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Mostrar formulario para subir el Excel de importación masiva.
     */
    public function importarForm()
    {
        return view('admin.capacitados.importar');
    }

    /**
     * Procesa el Excel subido y muestra una previsualización para confirmar.
     */
    public function importar(CapacitadoImportRequest $request, ImportacionCapacitadosService $servicio)
    {
        $resultado = $servicio->previsualizar($request->file('archivo_excel'));

        if ($resultado['token'] === null) {
            return back()->with('error', 'El archivo no contiene filas para importar.');
        }

        return view('admin.capacitados.importar-preview', [
            'token' => $resultado['token'],
            'filas' => $resultado['filas'],
            'resumen' => $resultado['resumen'],
        ]);
    }

    /**
     * Confirma la importación: crea/actualiza capacitados y genera
     * las solicitudes de certificación pendientes.
     */
    public function importarConfirmar(CapacitadoImportConfirmarRequest $request, ImportacionCapacitadosService $servicio)
    {
        try {
            $contadores = $servicio->confirmar($request->input('token'), $request->input('filas', []));
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.capacitados.importar.form')
                ->with('error', $e->getMessage());
        }

        $mensaje = "Importación completa: {$contadores['creados']} creados, "
            . "{$contadores['actualizados']} actualizados, "
            . "{$contadores['solicitudes_creadas']} solicitudes de certificación generadas.";

        return redirect()
            ->route('admin.capacitados.index')
            ->with('success', $mensaje);
    }
}
