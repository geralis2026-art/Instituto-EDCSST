<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracionRequest;
use App\Models\ConfiguracionSitio;

/** Gestión de la configuración general del sitio. Solo accesible para administradores. */
class ConfiguracionController extends Controller
{
    public function edit()
    {
        $config = ConfiguracionSitio::obtener();

        return view('admin.configuracion.edit', compact('config'));
    }

    public function update(ConfiguracionRequest $request)
    {
        ConfiguracionSitio::obtener()->update($request->validated());

        return redirect()->route('admin.configuracion.edit')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}
