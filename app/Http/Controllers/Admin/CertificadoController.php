<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificadoRequest;
use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
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

    public function create()
    {
        $capacitados = Capacitado::orderBy('nombre_completo')->get();
        $cursos = Curso::activos()->orderBy('nombre')->get();

        return view('admin.certificados.create', compact('capacitados', 'cursos'));
    }

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

    public function show(Certificado $certificado)
    {
        $certificado->load(['capacitado', 'curso.categoria', 'emitidoPor']);

        return view('admin.certificados.show', compact('certificado'));
    }

    public function edit(Certificado $certificado)
    {
        $capacitados = Capacitado::orderBy('nombre_completo')->get();
        $cursos = Curso::orderBy('nombre')->get();

        return view('admin.certificados.edit', compact('certificado', 'capacitados', 'cursos'));
    }

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

    public function verPdf(Certificado $certificado)
    {
        if (!$certificado->archivo_pdf || !Storage::disk('local')->exists($certificado->archivo_pdf)) {
            abort(404, 'El archivo PDF no se encuentra.');
        }

        return Storage::disk('local')->response($certificado->archivo_pdf, null, [
            'Content-Type' => 'application/pdf',
        ]);
    }

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
