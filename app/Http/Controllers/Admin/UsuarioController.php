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

    /** Formulario para editar un usuario existente. */
    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    /**
     * Actualiza nombre, email, rol y opcionalmente la contraseña. No puede
     * cambiarse el propio rol para evitar que un admin se bloquee a sí
     * mismo el acceso al panel (o deje el sistema sin ningún admin).
     */
    public function update(UsuarioRequest $request, User $usuario)
    {
        $datos = $request->validated();

        if ($usuario->id === Auth::id() && $datos['rol'] !== $usuario->rol) {
            return back()->withInput()->with('error', 'No puedes cambiar tu propio rol. Pídele a otro administrador que lo haga.');
        }

        $usuario->name  = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->rol   = $datos['rol'];

        if (!empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
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

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
