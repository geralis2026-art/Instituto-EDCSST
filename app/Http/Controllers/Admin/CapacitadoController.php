<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapacitadoRequest;
use App\Models\Capacitado;
use Illuminate\Http\Request;

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
            ->when($busqueda, function ($query, $busqueda) {
                $busqueda = trim($busqueda);
                return $query->where('nombre_completo', 'like', "%{$busqueda}%")
                             ->orWhere('documento', 'like', "%{$busqueda}%")
                             ->orWhere('correo', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre_completo')
            ->paginate(15);

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
     * Mostrar formulario para editar capacitado.
     */
    public function edit(Capacitado $capacitado)
    {
        return view('admin.capacitados.edit', compact('capacitado'));
    }

    /**
     * Actualizar capacitado en base de datos.
     */
    public function update(CapacitadoRequest $request, Capacitado $capacitado)
    {
        $capacitado->update($request->validated());

        return redirect()
            ->route('admin.capacitados.show', $capacitado)
            ->with('success', 'Capacitado actualizado correctamente.');
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
            ->get(['id', 'nombre_completo', 'documento']);

        return response()->json($resultados);
    }

    /**
     * Pendiente: descarga de plantilla Excel para importar capacitados
     * masivamente (Fase 2, no implementado).
     */
    public function descargarPlantilla()
    {
        // TODO: Implementar descarga de plantilla Excel
        // Por ahora, placeholder
        return back()->with('info', 'Funcionalidad de importación en construcción.');
    }

    /**
     * Pendiente: importación masiva de capacitados desde Excel
     * (Fase 2, no implementado).
     */
    public function importar(Request $request)
    {
        // TODO: Implementar importación masiva desde Excel
        // Por ahora, placeholder
        return back()->with('info', 'Funcionalidad de importación en construcción.');
    }
}
