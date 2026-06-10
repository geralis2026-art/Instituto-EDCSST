<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\Curso;
use App\Models\Certificado;
use App\Models\Mensaje;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Página principal del panel administrativo.
     * Muestra estadísticas generales y gráfica de certificados por mes.
     */
    public function index()
    {
        // Estadísticas generales
        $totalCapacitados = Capacitado::count();
        $totalCertificados = Certificado::where('activo', true)->count();
        $totalCursosActivos = Curso::where('activo', true)->count();
        $totalCategorias = \App\Models\Categoria::where('activo', true)->count();
        $mensajesNuevos = Mensaje::nuevos()->count();

        // Horas capacitadas totales
        $horasCapacitadasTotal = Certificado::where('activo', true)->sum('intensidad_horaria');

        // Certificados emitidos en los últimos 12 meses (para la gráfica)
        $certificadosPorMes = $this->certificadosPorMes();

        // Últimos 5 certificados emitidos
        $ultimosCertificados = Certificado::with(['capacitado', 'curso', 'emitidoPor'])
            ->where('activo', true)
            ->latest()
            ->take(5)
            ->get();

        // Top 5 capacitados por horas
        $topCapacitados = Capacitado::orderBy('horas_capacitadas', 'desc')
            ->take(5)
            ->get();

        // Cursos más usados
        $cursosMasUsados = Curso::withCount('certificados')
            ->where('activo', true)
            ->orderBy('certificados_count', 'desc')
            ->take(5)
            ->get();

        // Mensajes recientes
        $mensajesRecientes = Mensaje::latest()
            ->take(5)
            ->get();

        // Estadísticas de este mes
        $hoyInicio = Carbon::now()->startOfDay();
        $hoyFin = Carbon::now()->endOfDay();
        $certificadosHoy = Certificado::whereBetween('created_at', [$hoyInicio, $hoyFin])->count();

        $mesInicio = Carbon::now()->startOfMonth();
        $mesFin = Carbon::now()->endOfMonth();
        $certificadosMesActual = Certificado::whereBetween('fecha_emision', [$mesInicio, $mesFin])->count();
        $capacitadosMesActual = Capacitado::whereBetween('created_at', [$mesInicio, $mesFin])->count();

        return view('admin.dashboard', compact(
            'totalCapacitados',
            'totalCertificados',
            'totalCursosActivos',
            'totalCategorias',
            'horasCapacitadasTotal',
            'mensajesNuevos',
            'certificadosPorMes',
            'ultimosCertificados',
            'topCapacitados',
            'cursosMasUsados',
            'mensajesRecientes',
            'certificadosHoy',
            'certificadosMesActual',
            'capacitadosMesActual'
        ));
    }

    /**
     * Devuelve un array con la cantidad de certificados emitidos por mes
     * en los últimos 12 meses. Formato para Chart.js.
     */
    private function certificadosPorMes(): array
    {
        $inicio = Carbon::now()->subMonths(11)->startOfMonth();

        $resultados = Certificado::where('activo', true)
            ->where('fecha_emision', '>=', $inicio)
            ->selectRaw("DATE_FORMAT(fecha_emision, '%Y-%m') as mes, COUNT(*) as total")
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $meses = [];
        $cantidades = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $meses[] = $fecha->locale('es')->isoFormat('MMM YYYY');
            $cantidades[] = $resultados[$fecha->format('Y-m')] ?? 0;
        }

        return ['labels' => $meses, 'data' => $cantidades];
    }
}
