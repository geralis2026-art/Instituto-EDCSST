<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracionRequest;
use App\Models\ConfiguracionSitio;
use Illuminate\Support\Facades\Storage;

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
        $datos = collect($request->validated())
            ->except('plantilla_certificado_archivo')
            ->all();

        if ($request->hasFile('plantilla_certificado_archivo')) {
            Storage::disk('public')->putFileAs(
                'plantillas',
                $request->file('plantilla_certificado_archivo'),
                'certificado.pdf'
            );
            $datos['plantilla_certificado'] = 'plantillas/certificado.pdf';
        }

        ConfiguracionSitio::obtener()->update($datos);

        return redirect()->route('admin.configuracion.edit')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}
