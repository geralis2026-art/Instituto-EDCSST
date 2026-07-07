<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\Curso;
use Illuminate\Validation\Rule;
use App\Models\SolicitudCertificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RegistroCapacitadoController extends Controller
{
    private function tokenValido(string $token): bool
    {
        return Cache::has("reg:{$token}");
    }

    public function form(string $token)
    {
        if (!$this->tokenValido($token)) {
            return view('public.registro-expirado');
        }

        $cursos = Curso::activos()
            ->with('categoria')
            ->orderBy('nombre')
            ->get()
            ->groupBy(fn ($c) => $c->categoria?->nombre ?? 'Sin categoría');

        return view('public.registro', compact('token', 'cursos'));
    }

    public function guardar(string $token, Request $request)
    {
        if (!$this->tokenValido($token)) {
            return view('public.registro-expirado');
        }

        $datos = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'tipo_documento'  => ['nullable', Rule::in(array_keys(Capacitado::TIPOS_DOCUMENTO))],
            'documento'       => 'required|string|max:50',
            'correo'          => 'nullable|email|max:255',
            'telefono'        => 'nullable|string|max:30',
            'rh'              => 'nullable|string|max:10',
            'cursos'          => 'required|array|min:1',
            'cursos.*'        => 'required|integer|exists:cursos,id',
            'modalidades'     => 'required|array|min:1',
            'modalidades.*'   => 'required|in:virtual,presencial',
        ], [
            'nombre_completo.required' => 'El nombre completo es requerido.',
            'tipo_documento.in'        => 'El tipo de documento no es válido.',
            'documento.required'       => 'El número de documento es requerido.',
            'cursos.required'          => 'Debes seleccionar al menos un curso.',
            'cursos.min'               => 'Debes seleccionar al menos un curso.',
            'cursos.*.exists'          => 'Uno de los cursos seleccionados no es válido.',
            'modalidades.required'     => 'Debes seleccionar la modalidad de cada curso.',
            'modalidades.*.required'   => 'Selecciona la modalidad de cada curso marcado.',
            'modalidades.*.in'         => 'La modalidad debe ser presencial o virtual.',
        ]);

        $capacitado = Capacitado::updateOrCreate(
            ['documento' => trim($datos['documento'])],
            [
                'nombre_completo' => $datos['nombre_completo'],
                'tipo_documento'  => $datos['tipo_documento'] ?? 'CC',
                'correo'          => $datos['correo'] ?? null,
                'telefono'        => $datos['telefono'] ?? null,
                'rh'              => $datos['rh'] ?? null,
            ]
        );

        $cursosActivos = Curso::activos()->whereIn('id', $datos['cursos'])->pluck('id');

        foreach ($cursosActivos as $cursoId) {
            SolicitudCertificado::firstOrCreate(
                [
                    'capacitado_id' => $capacitado->id,
                    'curso_id'      => $cursoId,
                    'estado'        => SolicitudCertificado::ESTADO_PENDIENTE,
                ],
                [
                    'modalidad' => $datos['modalidades'][$cursoId] ?? 'presencial',
                    'origen'    => 'registro_link',
                ]
            );
        }

        return view('public.registro-exito', ['nombre' => $datos['nombre_completo']]);
    }
}
