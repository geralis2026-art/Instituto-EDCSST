<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $usuario->activo = false;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado. Actívalo para que pueda ingresar.');
    }

    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }

        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return back()->with('success', $usuario->activo ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}
