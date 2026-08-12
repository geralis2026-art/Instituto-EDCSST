<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Mensaje;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Página de inicio del panel administrativo: estadísticas generales,
 * gráfica de certificados emitidos por mes y rankings (top capacitados,
 * cursos más usados).
 */
class DashboardController extends Controller
{
    /**
     * Página principal del panel administrativo.
     * Muestra estadísticas generales y gráfica de certificados por mes.
     */
    public function index()
    {
        $now = Carbon::now();
        $user = auth()->user();

        // Estadísticas escalares cacheadas 60 s — evita golpear la BD en cada recarga.
        // La clave incluye el user_id porque totalCapacitados/totalCertificados/etc.
        // pasan por Eloquent y quedan scoped por PropietarioScope: una clave fija
        // compartida haría que el primero en cargar el dashboard dentro de esos 60 s
        // le "prestara" sus números (todos o solo los propios) a cualquier otro
        // usuario que entrara después, mezclando datos entre instructores/admin.
        $stats = Cache::remember("dashboard_stats:{$user->id}", 60, function () use ($now, $user) {
            return [
                'totalCapacitados'      => Capacitado::count(),
                'totalCertificados'     => Certificado::where('activo', true)->count(),
                'totalCursosActivos'    => Curso::where('activo', true)->count(),
                'totalCategorias'       => Categoria::where('activo', true)->count(),
                'mensajesNuevos'        => $user->isAdmin() ? Mensaje::nuevos()->count() : null,
                'horasCapacitadasTotal' => Certificado::where('activo', true)->sum('intensidad_horaria'),
                'certificadosPorMes'    => $this->certificadosPorMes(),
                'certificadosHoy'       => Certificado::whereDate('created_at', today())->count(),
                'certificadosMesActual' => Certificado::whereBetween('fecha_emision', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth(),
                ])->count(),
                'capacitadosMesActual'  => Capacitado::whereBetween('created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth(),
                ])->count(),
            ];
        });

        // Colecciones Eloquent: consultadas en vivo (nunca se cachean — file cache no serializa modelos).
        $ultimosCertificados = Certificado::with(['capacitado', 'curso', 'emitidoPor'])
            ->where('activo', true)
            ->latest()
            ->take(5)
            ->get();

        $topCapacitados = Capacitado::orderBy('horas_capacitadas', 'desc')
            ->take(5)
            ->get();

        $cursosMasUsados = Curso::withCount('certificados')
            ->where('activo', true)
            ->orderBy('certificados_count', 'desc')
            ->take(5)
            ->get();

        // Los mensajes de contacto son una sección exclusiva de admin
        // (ver routes/web.php); no se cargan para capacitador/instructor.
        $mensajesRecientes = $user->isAdmin()
            ? Mensaje::latest()->take(5)->get()
            : collect();

        return view('admin.dashboard', [
            ...$stats,
            'ultimosCertificados' => $ultimosCertificados,
            'topCapacitados'      => $topCapacitados,
            'cursosMasUsados'     => $cursosMasUsados,
            'mensajesRecientes'   => $mensajesRecientes,
        ]);
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

        $meses      = [];
        $cantidades = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha        = Carbon::now()->subMonths($i);
            $meses[]      = $fecha->locale('es')->isoFormat('MMM YYYY');
            $cantidades[] = $resultados[$fecha->format('Y-m')] ?? 0;
        }

        return ['labels' => $meses, 'data' => $cantidades];
    }
}
