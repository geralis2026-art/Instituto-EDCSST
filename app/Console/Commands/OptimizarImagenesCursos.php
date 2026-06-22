<?php

namespace App\Console\Commands;

use App\Models\Curso;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OptimizarImagenesCursos extends Command
{
    protected $signature   = 'cursos:optimizar-imagenes';
    protected $description = 'Convierte las imágenes existentes de cursos a WebP y las redimensiona a máx 800 px.';

    public function handle(): int
    {
        if (!extension_loaded('gd')) {
            $this->error('La extensión GD de PHP no está disponible.');
            return self::FAILURE;
        }

        $cursos = Curso::whereNotNull('imagen')->get();

        if ($cursos->isEmpty()) {
            $this->info('No hay cursos con imagen.');
            return self::SUCCESS;
        }

        $this->info("Se procesarán {$cursos->count()} curso(s).");
        $bar = $this->output->createProgressBar($cursos->count());
        $bar->start();

        $convertidos = 0;
        $errores     = 0;

        foreach ($cursos as $curso) {
            $bar->advance();

            $relPath = $curso->imagen;

            if (!Storage::disk('uploads')->exists($relPath)) {
                $this->newLine();
                $this->warn("  [{$curso->nombre}] Archivo no encontrado: {$relPath}");
                $errores++;
                continue;
            }

            // Ya es WebP → omitir
            if (str_ends_with(strtolower($relPath), '.webp')) {
                continue;
            }

            $absPath = Storage::disk('uploads')->path($relPath);
            $mime    = mime_content_type($absPath);

            $src = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($absPath),
                'image/png'  => @imagecreatefrompng($absPath),
                default      => false,
            };

            if (!$src) {
                $this->newLine();
                $this->warn("  [{$curso->nombre}] Formato no soportado: {$mime}");
                $errores++;
                continue;
            }

            $origW = imagesx($src);
            $origH = imagesy($src);
            $maxW  = 800;

            if ($origW > $maxW) {
                $newW = $maxW;
                $newH = (int) round($origH * ($maxW / $origW));
            } else {
                $newW = $origW;
                $newH = $origH;
            }

            $dst = imagecreatetruecolor($newW, $newH);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            $nuevoRel  = 'cursos/' . Str::uuid() . '.webp';
            $nuevoAbs  = Storage::disk('uploads')->path($nuevoRel);

            if (!imagewebp($dst, $nuevoAbs, 82)) {
                $this->newLine();
                $this->warn("  [{$curso->nombre}] No se pudo escribir WebP.");
                imagedestroy($src);
                imagedestroy($dst);
                $errores++;
                continue;
            }

            imagedestroy($src);
            imagedestroy($dst);

            // Actualizar BD y eliminar archivo original
            $curso->update(['imagen' => $nuevoRel]);
            Storage::disk('uploads')->delete($relPath);

            $convertidos++;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Completado: {$convertidos} convertida(s), {$errores} error(es).");

        return self::SUCCESS;
    }
}
