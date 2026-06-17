<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\Curso;
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
            'documento'       => 'required|string|max:50',
            'correo'          => 'nullable|email|max:255',
            'telefono'        => 'nullable|string|max:30',
            'rh'              => 'nullable|string|max:10',
            'modalidad'       => 'required|in:virtual,presencial',
            'cursos'          => 'required|array|min:1',
            'cursos.*'        => 'required|integer|exists:cursos,id',
        ], [
            'nombre_completo.required' => 'El nombre completo es requerido.',
            'documento.required'       => 'El número de documento es requerido.',
            'modalidad.required'       => 'Debes seleccionar la modalidad.',
            'cursos.required'          => 'Debes seleccionar al menos un curso.',
            'cursos.min'               => 'Debes seleccionar al menos un curso.',
            'cursos.*.exists'          => 'Uno de los cursos seleccionados no es válido.',
        ]);

        $capacitado = Capacitado::updateOrCreate(
            ['documento' => trim($datos['documento'])],
            [
                'nombre_completo' => $datos['nombre_completo'],
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
                    'estado'        => 'pendiente',
                ],
                [
                    'modalidad' => $datos['modalidad'],
                    'origen'    => 'registro_link',
                ]
            );
        }

        return view('public.registro-exito', ['nombre' => $datos['nombre_completo']]);
    }
}
