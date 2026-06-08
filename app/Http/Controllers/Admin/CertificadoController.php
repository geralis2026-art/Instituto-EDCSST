<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificadoRequest;
use App\Models\Capacitado;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
    /** Lista paginada de certificados con búsqueda por código/capacitado y filtro por curso. */
    public function index(Request $request)
    {
        $busqueda = $request->query('busqueda', '');
        $cursoId = $request->query('curso_id', '');

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
    public function store(CertificadoRequest $request)
    {
        $datos = $request->validated();
        $codigoManual = $datos['codigo_unico'] ?: null;
        $datos['codigo_unico'] = $codigoManual ?? (string) Str::uuid();
        $datos['emitido_por'] = auth()->id();
        $datos['archivo_pdf'] = $request->file('archivo_pdf')->store('certificados');
        $datos['fecha_vencimiento'] = \Carbon\Carbon::parse($datos['fecha_emision'])->addYear()->toDateString();

        $certificado = Certificado::create($datos);

        if (!$codigoManual) {
            $certificado->codigo_unico = Certificado::generarCodigoUnico($certificado->id);
            $certificado->saveQuietly();
        }

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

    private function buildCategoriasJson($categorias): string
    {
        return json_encode($categorias->map(fn ($cat) => [
            'id'     => $cat->id,
            'nombre' => $cat->nombre,
            'cursos' => $cat->cursos->map(fn ($c) => ['id' => $c->id, 'nombre' => $c->nombre])->values(),
        ])->values());
    }

    /** Actualiza el certificado. Si se sube nuevo PDF, elimina el anterior del storage. */
    public function update(CertificadoRequest $request, Certificado $certificado)
    {
        $datos = $request->validated();
        $datos['fecha_vencimiento'] = \Carbon\Carbon::parse($datos['fecha_emision'])->addYear()->toDateString();

        if ($request->hasFile('archivo_pdf')) {
            if ($certificado->archivo_pdf) {
                Storage::disk('local')->delete($certificado->archivo_pdf);
            }

            $datos['archivo_pdf'] = $request->file('archivo_pdf')->store('certificados');
        } else {
            unset($datos['archivo_pdf']);
        }

        $certificado->update($datos);

        return redirect()
            ->route('admin.certificados.show', $certificado)
            ->with('success', 'Certificado actualizado correctamente.');
    }

    /** Sirve el PDF del certificado directamente en el navegador (inline). */
    public function verPdf(Certificado $certificado)
    {
        if (!$certificado->archivo_pdf || !Storage::disk('local')->exists($certificado->archivo_pdf)) {
            abort(404, 'El archivo PDF no se encuentra.');
        }

        return Storage::disk('local')->response($certificado->archivo_pdf, null, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /** Elimina el certificado y su PDF del storage. */
    public function destroy(Certificado $certificado)
    {
        if ($certificado->archivo_pdf) {
            Storage::disk('local')->delete($certificado->archivo_pdf);
        }

        $certificado->delete();

        return redirect()
            ->route('admin.certificados.index')
            ->with('success', 'Certificado eliminado correctamente.');
    }

    public function masivosForm()
    {
        return redirect()->route('admin.certificados.index')
            ->with('info', 'La generación masiva estará disponible próximamente.');
    }

    public function generarMasivos(Request $request)
    {
        return redirect()->route('admin.certificados.index')
            ->with('info', 'La generación masiva estará disponible próximamente.');
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
