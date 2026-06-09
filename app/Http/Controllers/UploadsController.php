<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class UploadsController extends Controller
{
    /**
     * Sirve archivos del disco 'uploads' (imágenes de cursos, logos).
     * El tipo restringe el directorio permitido; el nombre valida el archivo.
     */
    public function serve(string $type, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!preg_match('/^[\w\-]+\.(jpg|jpeg|png|gif|webp|svg)$/i', $filename)) {
            abort(404);
        }

        $path = "{$type}/{$filename}";

        if (!Storage::disk('uploads')->exists($path)) {
            abort(404);
        }

        return Storage::disk('uploads')->response($path, null, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }
}
