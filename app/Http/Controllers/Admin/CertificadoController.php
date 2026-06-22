<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificadoRequest;
use App\Models\Capacitado;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Curso;
use App\Services\CertificadoPdfService;
use App\Services\GeneracionMasivaCertificadosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Gestión de certificados emitidos a capacitados.
 * Acceso de lectura/creación para admin y capacitador; edición,
 * eliminación y activación/desactivación solo para admin (ver routes/web.php).
 */
class CertificadoController extends Controller
{
    /** Lista paginada de certificados con búsqueda por código/capacitado y filtro por curso. */
    public function index(Request $request)
    {
        $busqueda = substr(trim((string) $request->query('busqueda', '')), 0, 100);
        $cursoId  = (int) $request->query('curso_id', 0) ?: '';

        $cursos = Curso::orderBy('nombre')->get();

        $certificados = Certificado::with(['capacitado', 'curso'])
            ->when($busqueda, function ($query, $busqueda) {
                $busqueda = trim($busqueda);

                return $query->where(function ($query) use ($busqueda) {
                    $query->where('codigo_unico', 'like', "%{$busqueda}%")
                        ->orWhereHas('capacitado', function ($query) use ($busqueda) {
                            $query->where('nombre_completo', 'like', "%{$busqueda}%")
                                ->orWhere('documento', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->when($cursoId, fn ($query) => $query->where('curso_id', $cursoId))
            ->latest('fecha_emision')
            ->paginate(15);

        return view('admin.certificados.index', compact('certificados', 'cursos', 'busqueda', 'cursoId'));
    }

    /** Formulario para registrar un nuevo certificado. */
    public function create()
    {
        $categorias = Categoria::activas()
            ->with(['cursos' => fn ($q) => $q->activos()->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        $categoriasJson = $this->buildCategoriasJson($categorias);

        return view('admin.certificados.create', compact('categorias', 'categoriasJson'));
    }

    /**
     * Guarda el certificado: genera código único, calcula fecha de vencimiento (+1 año)
     * y almacena el PDF en storage/app/certificados/.
     */
    public function store(CertificadoRequest $request, CertificadoPdfService $pdfService)
    {
        $datos = $request->validated();
        $aniosVigencia = (int) $datos['anios_vigencia'];
        unset($datos['anios_vigencia']);
        $codigoManual = $datos['codigo_unico'] ?: null;
        $datos['codigo_unico'] = $codigoManual ?? (string) Str::uuid();
        $datos['emitido_por'] = auth()->id();
        $datos['fecha_vencimiento'] = \Carbon\Carbon::parse($datos['fecha_emision'])->addYears($aniosVigencia)->toDateString();

        if ($request->hasFile('archivo_pdf')) {
            $datos['archivo_pdf'] = $request->file('archivo_pdf')->store('certificados', 'certificados');
        } else {
            unset($datos['archivo_pdf']);
        }

        /**
         * Transacción atómica: si generarCodigoUnico() o saveQuietly() fallan,
         * el rollback elimina el registro con UUID temporal y la BD queda limpia.
         */
        $certificado = DB::transaction(function () use ($datos, $codigoManual, $request, $pdfService) {
            $certificado = Certificado::create($datos);

            if (!$codigoManual) {
                $certificado->codigo_unico = Certificado::generarCodigoUnico();
            }

            if (!$request->hasFile('archivo_pdf')) {
                $certificado->archivo_pdf = $pdfService->generarYGuardar($certificado);
            }

            $certificado->saveQuietly();

            return $certificado;
        });

        return redirect()
            ->route('admin.certificados.show', $certificado)
            ->with('success', 'Certificado registrado correctamente.');
    }

    /** Detalle completo del certificado con capacitado, curso y quién lo emitió. */
    public function show(Certificado $certificado)
    {
        $certificado->load(['capacitado', 'curso.categoria', 'emitidoPor']);

        return view('admin.certificados.show', compact('certificado'));
    }

    /** Formulario para editar un certificado existente. */
    public function edit(Certificado $certificado)
    {
        $categorias = Categoria::activas()
            ->with(['cursos' => fn ($q) => $q->activos()->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        $categoriasJson = $this->buildCategoriasJson($categorias);
        $certificado->load('capacitado');

        return view('admin.certificados.edit', compact('certificado', 'categorias', 'categoriasJson'));
    }

    /**
     * Convierte las categorías y sus cursos a JSON para el selector dependiente del formulario.
     * JSON_THROW_ON_ERROR garantiza que nunca se inyecte `false` literal en el atributo x-data.
     * JSON_UNESCAPED_UNICODE preserva tildes y ñ en los nombres sin secuencias \uXXXX.
     */
    private function buildCategoriasJson($categorias): string
    {
        return json_encode(
            $categorias->map(fn ($cat) => [
                'id'     => $cat->id,
                'nombre' => $cat->nombre,
                'cursos' => $cat->cursos->map(fn ($c) => ['id' => $c->id, 'nombre' => $c->nombre])->values(),
            ])->values(),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        );
    }

    /** Actualiza el certificado. Si se sube PDF manual lo usa; si no, regenera desde la plantilla. */
    public function update(CertificadoRequest $request, Certificado $certificado, CertificadoPdfService $pdfService)
    {
        $datos = $request->validated();
        $aniosVigencia = (int) $datos['anios_vigencia'];
        unset($datos['anios_vigencia']);
        $datos['fecha_vencimiento'] = \Carbon\Carbon::parse($datos['fecha_emision'])->addYears($aniosVigencia)->toDateString();

        if ($request->hasFile('archivo_pdf')) {
            if ($certificado->archivo_pdf) {
                Storage::disk('certificados')->delete($certificado->archivo_pdf);
            }
            $datos['archivo_pdf'] = $request->file('archivo_pdf')->store('certificados', 'certificados');
        } else {
            unset($datos['archivo_pdf']);
        }

        $certificado->update($datos);

        if (!$request->hasFile('archivo_pdf')) {
            if ($certificado->archivo_pdf) {
                Storage::disk('certificados')->delete($certificado->archivo_pdf);
            }
            $certificado->refresh();
            $certificado->archivo_pdf = $pdfService->generarYGuardar($certificado);
            $certificado->saveQuietly();
        }

        $mensaje = $request->hasFile('archivo_pdf')
            ? 'Certificado actualizado correctamente.'
            : 'Certificado actualizado y PDF regenerado correctamente.';

        return redirect()
            ->route('admin.certificados.show', $certificado)
            ->with('success', $mensaje);
    }

    /**
     * Sirve el PDF del certificado directamente en el navegador (inline).
     * Usa el archivo almacenado si existe; solo regenera si no hay archivo,
     * evitando re-renderizado costoso con FPDI en cada visualización.
     */
    public function verPdf(Certificado $certificado, CertificadoPdfService $pdfService)
    {
        if ($certificado->archivo_pdf && Storage::disk('certificados')->exists($certificado->archivo_pdf)) {
            $pdf = Storage::disk('certificados')->get($certificado->archivo_pdf);
        } else {
            $pdf = $pdfService->generarPdf($certificado);
        }

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $certificado->codigo_unico . '.pdf"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
        ]);
    }

    /** Elimina el certificado y su PDF del storage. */
    public function destroy(Certificado $certificado)
    {
        if ($certificado->archivo_pdf) {
            Storage::disk('certificados')->delete($certificado->archivo_pdf);
        }

        $certificado->delete();

        return redirect()
            ->route('admin.certificados.index')
            ->with('success', 'Certificado eliminado correctamente.');
    }

    /** Formulario de generación masiva: lista las solicitudes de certificación pendientes. */
    public function masivosForm(GeneracionMasivaCertificadosService $servicio)
    {
        $solicitudes = $servicio->solicitudesPendientes();
        $cursos = Curso::activos()->orderBy('nombre')->get();

        return view('admin.certificados.masivos', compact('solicitudes', 'cursos'));
    }

    /**
     * Genera en lote los certificados de las solicitudes seleccionadas.
     * Filtra primero las filas marcadas para incluir y valida solo esas,
     * evitando reglas contradictoras (required_with + nullable) sobre filas
     * que el usuario no seleccionó y que nunca se procesarán.
     */
    public function generarMasivos(Request $request, GeneracionMasivaCertificadosService $servicio, CertificadoPdfService $pdfService)
    {
        $incluidas = array_filter(
            $request->input('solicitudes', []),
            fn ($fila) => !empty($fila['incluir'])
        );

        if (empty($incluidas)) {
            return back()->with('error', 'No seleccionaste ninguna solicitud para generar.');
        }

        $validator = Validator::make(
            ['solicitudes' => $incluidas],
            [
                'solicitudes.*.curso_id'          => 'required|exists:cursos,id',
                'solicitudes.*.fecha_emision'      => 'required|date',
                'solicitudes.*.intensidad_horaria' => 'required|integer|min:1|max:500',
                'solicitudes.*.modalidad'          => 'nullable|in:virtual,presencial',
                'solicitudes.*.anios_vigencia'     => 'required|integer|in:1,2',
                'solicitudes.*.activo'             => 'nullable|boolean',
            ],
            [
                'solicitudes.*.curso_id.required'          => 'El curso es requerido para cada solicitud seleccionada.',
                'solicitudes.*.curso_id.exists'            => 'Uno de los cursos seleccionados no es válido.',
                'solicitudes.*.fecha_emision.required'     => 'La fecha de emisión es requerida para cada solicitud seleccionada.',
                'solicitudes.*.intensidad_horaria.required' => 'La intensidad horaria es requerida para cada solicitud seleccionada.',
                'solicitudes.*.anios_vigencia.required'    => 'La vigencia es requerida para cada solicitud seleccionada.',
                'solicitudes.*.anios_vigencia.in'          => 'La vigencia debe ser 1 o 2 años.',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $filas = [];

        foreach ($validator->validated()['solicitudes'] as $solicitudId => $fila) {
            $filas[] = [
                'solicitud_id'      => (int) $solicitudId,
                'curso_id'          => (int) $fila['curso_id'],
                'fecha_emision'     => $fila['fecha_emision'],
                'intensidad_horaria' => (int) $fila['intensidad_horaria'],
                'modalidad'         => $fila['modalidad'] ?? null,
                'anios_vigencia'    => (int) $fila['anios_vigencia'],
                'activo'            => (bool) ($fila['activo'] ?? true),
            ];
        }

        $resultado = $servicio->generar($filas, auth()->id(), $pdfService);

        $mensaje = "{$resultado['generados']} certificado(s) generado(s) correctamente.";

        if (!empty($resultado['errores'])) {
            $mensaje .= ' Errores: ' . implode(' | ', $resultado['errores']);

            return redirect()->route('admin.certificados.masivos')->with('error', $mensaje);
        }

        return redirect()->route('admin.certificados.index')->with('success', $mensaje);
    }

    /**
     * Regenera el PDF del certificado desde la plantilla institucional y
     * actualiza el archivo almacenado, sin modificar ningún otro dato.
     */
    public function regenerarPdf(Certificado $certificado, CertificadoPdfService $pdfService)
    {
        if ($certificado->archivo_pdf) {
            Storage::disk('certificados')->delete($certificado->archivo_pdf);
        }

        $certificado->archivo_pdf = $pdfService->generarYGuardar($certificado);
        $certificado->saveQuietly();

        return redirect()
            ->route('admin.certificados.show', $certificado)
            ->with('success', 'PDF regenerado correctamente.');
    }

    /** Activa o desactiva el certificado sin eliminarlo. Recalcula horas del capacitado vía evento. */
    public function toggleActivo(Certificado $certificado)
    {
        $certificado->update([
            'activo' => ! $certificado->activo,
        ]);

        $mensaje = $certificado->activo
            ? 'Certificado reactivado correctamente.'
            : 'Certificado desactivado correctamente.';

        return back()->with('success', $mensaje);
    }
}
