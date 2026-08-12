<?php

namespace App\Http\Controllers\Aula;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Panel principal del capacitado: lista sus cursos matriculados con su avance.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $capacitado = Auth::guard('capacitados')->user();

        // 'curso.modulos' y 'progresoModulos' se cargan aquí para que el accessor
        // porcentaje_avance (usado en la vista) no dispare 2 consultas por cada
        // curso matriculado.
        $matriculas = $capacitado->matriculas()
            ->with(['curso.modulos' => fn ($q) => $q->activos(), 'progresoModulos'])
            ->latest('fecha_asignacion')
            ->get();

        return view('aula.dashboard', compact('matriculas'));
    }
}
