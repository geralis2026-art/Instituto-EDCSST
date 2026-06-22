<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MensajeUpdateRequest;
use App\Models\Mensaje;
use Illuminate\Http\Request;

/**
 * Bandeja de mensajes del formulario de contacto público.
 * Acceso exclusivo para admin (ver routes/web.php).
 */
class MensajeController extends Controller
{
    /** Bandeja de mensajes paginada con búsqueda por nombre/correo y filtro por estado. */
    public function index(Request $request)
    {
        $busqueda = substr(trim((string) $request->query('busqueda', '')), 0, 100);
        $estado   = in_array($request->query('estado'), ['nuevo', 'leido', 'respondido'])
            ? $request->query('estado')
            : null;

        $mensajes = Mensaje::query()
            ->when($busqueda, fn ($q) =>
                $q->where(fn ($inner) =>
                    $inner->where('nombre', 'like', "%{$busqueda}%")
                          ->orWhere('correo', 'like', "%{$busqueda}%")
                )
            )
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.mensajes.index', compact('mensajes', 'busqueda', 'estado'));
    }

    /** Muestra el mensaje y lo marca como leído automáticamente si era nuevo. */
    public function show(Mensaje $mensaje)
    {
        if ($mensaje->estado === Mensaje::ESTADO_NUEVO) {
            $mensaje->marcarComoLeido();
        }

        return view('admin.mensajes.show', compact('mensaje'));
    }

    /** Actualiza el estado y/o las notas internas del mensaje. */
    public function update(MensajeUpdateRequest $request, Mensaje $mensaje)
    {
        $mensaje->update($request->validated());

        return back()->with('success', 'Mensaje actualizado.');
    }

    /** Elimina permanentemente el mensaje. */
    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();

        return redirect()
            ->route('admin.mensajes.index')
            ->with('success', 'Mensaje eliminado.');
    }
}
