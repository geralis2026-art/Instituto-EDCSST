<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificacionRequest;
use App\Models\Certificado;

/**
 * Verificación pública de autenticidad de certificados por código
 * único (para que terceros confirmen validez y vigencia).
 */
class VerificacionController extends Controller
{
    /** Muestra el formulario de verificación pública. */
    public function index()
    {
        return view('public.verificar');
    }

    /** Verifica la autenticidad de un certificado por su código único. */
    public function verificar(VerificacionRequest $request)
    {
        $codigo = strtoupper($request->validated()['codigo']);

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
