<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use Illuminate\Http\Request;

class VerificacionController extends Controller
{
    /**
     * Muestra el formulario de verificación pública.
     */
    public function index()
    {
        return view('public.verificar');
    }

    /**
     * Verifica la autenticidad de un certificado por su código único.
     */
    public function verificar(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50',
        ], [
            'codigo.required' => 'Por favor ingresa el código del certificado.',
        ]);

        $codigo = strtoupper(trim($datos['codigo']));
        $certificado = Certificado::with(['capacitado', 'curso.categoria'])
            ->where('codigo_unico', $codigo)
            ->where('activo', true)
            ->first();

        $vencido = $certificado && $certificado->isVencido();

        return view('public.verificar', compact('certificado', 'vencido'))
            ->with('verificacionRealizada', true)
            ->with('codigoBuscado', $codigo);
    }
}
