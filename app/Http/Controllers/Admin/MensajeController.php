<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        $mensajes = Mensaje::query()
            ->when($request->busqueda, fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
                  ->orWhere('correo', 'like', "%{$v}%")
            )
            ->when($request->estado, fn($q, $v) => $q->where('estado', $v))
            ->latest()
            ->paginate(20);

        return view('admin.mensajes.index', compact('mensajes'));
    }

    public function show(Mensaje $mensaje)
    {
        if ($mensaje->estado === Mensaje::ESTADO_NUEVO) {
            $mensaje->marcarComoLeido();
        }
        return view('admin.mensajes.show', compact('mensaje'));
    }

    public function update(Request $request, Mensaje $mensaje)
    {
        $request->validate([
            'estado'         => 'required|in:nuevo,leido,respondido',
            'notas_internas' => 'nullable|string|max:2000',
        ]);

        $mensaje->update($request->only('estado', 'notas_internas'));

        return back()->with('success', 'Mensaje actualizado.');
    }

    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();
        return redirect()->route('admin.mensajes.index')->with('success', 'Mensaje eliminado.');
    }
}
