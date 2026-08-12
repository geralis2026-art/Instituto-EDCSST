<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\Curso;
use App\Models\Matricula;
use App\Notifications\CapacitadoBienvenida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Asigna capacitados a un curso del aula virtual (matrícula). Si el
 * capacitado nunca tuvo acceso al aula virtual, su contraseña inicial
 * se fija como su número de documento (cambio obligatorio en el primer
 * ingreso, ver PasswordCambioController) y se le notifica por correo
 * (ver CapacitadoBienvenida).
 */
class MatriculaController extends Controller
{
    public function index(Curso $curso)
    {
        $matriculas = $curso->matriculas()->with('capacitado')->latest('fecha_asignacion')->get();

        return view('admin.matriculas.index', compact('curso', 'matriculas'));
    }

    public function store(Request $request, Curso $curso)
    {
        if (!$curso->tiene_aula_virtual) {
            return back()->with('error', 'Este curso no tiene el aula virtual habilitada. Actívala desde "Editar curso" antes de matricular capacitados.');
        }

        $datos = $request->validate([
            'capacitado_id' => ['required', 'integer', 'exists:capacitados,id'],
        ], [
            'capacitado_id.required' => 'Busca y selecciona un capacitado.',
            'capacitado_id.exists'   => 'El capacitado seleccionado no es válido.',
        ]);

        $capacitado = Capacitado::find($datos['capacitado_id']);

        if (!$capacitado->correo) {
            return back()->withInput()->with('error', 'Este capacitado no tiene correo registrado. Agrégaselo desde su ficha antes de matricularlo al aula virtual.');
        }

        if (Matricula::where('curso_id', $curso->id)->where('capacitado_id', $capacitado->id)->exists()) {
            return back()->withInput()->with('error', 'Este capacitado ya está matriculado en este curso.');
        }

        $esPrimerAcceso = !$capacitado->password;

        if ($esPrimerAcceso) {
            $capacitado->forceFill([
                'password' => Hash::make($capacitado->documento),
                'debe_cambiar_password' => true,
            ])->save();
        }

        Matricula::create([
            'curso_id' => $curso->id,
            'capacitado_id' => $capacitado->id,
            'fecha_asignacion' => now(),
        ]);

        $capacitado->notify(new CapacitadoBienvenida($curso, $esPrimerAcceso));

        return redirect()
            ->route('admin.cursos.matriculas.index', $curso)
            ->with('success', "Se matriculó a {$capacitado->nombre_completo} y se le envió la notificación por correo.");
    }

    public function destroy(Curso $curso, Matricula $matricula)
    {
        abort_unless($matricula->curso_id === $curso->id, 404);

        if ($matricula->completado) {
            return back()->with('error', 'No se puede quitar esta matrícula porque el capacitado ya la completó. Gestiona el certificado desde su ficha si es necesario.');
        }

        $matricula->delete();

        return redirect()
            ->route('admin.cursos.matriculas.index', $curso)
            ->with('success', 'Matrícula eliminada correctamente.');
    }
}
