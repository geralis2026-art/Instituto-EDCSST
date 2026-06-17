<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Gestión de usuarios (empleados) del sistema. Acceso exclusivo
 * para admin (ver routes/web.php).
 */
class UsuarioController extends Controller
{
    /** Lista todos los usuarios (empleados) del sistema. */
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(15);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    /** Formulario para crear un nuevo usuario. */
    public function create()
    {
        return view('admin.usuarios.create');
    }

    /** Crea el usuario con rol asignado. Se crea inactivo por defecto — el admin debe activarlo. */
    public function store(UsuarioRequest $request)
    {
        $datos = $request->validated();

        User::create([
            'name'     => $datos['name'],
            'email'    => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol'      => $datos['rol'],
            'activo'   => false,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado. Actívalo para que pueda ingresar.');
    }

    /** Activa o desactiva un usuario. No puede aplicarse al usuario autenticado en sesión. */
    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }

        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return back()->with('success', $usuario->activo ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    /** Elimina el usuario. No puede aplicarse al usuario autenticado en sesión. */
    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}
