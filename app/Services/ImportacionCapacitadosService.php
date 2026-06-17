<?php

namespace App\Services;

use App\Models\Capacitado;
use App\Models\Curso;
use App\Models\SolicitudCertificado;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Procesa la importación masiva de capacitados desde un archivo Excel.
 *
 * La columna "cursos" acepta uno o varios nombres separados por coma.
 * La búsqueda de cursos es parcial: "violencia sexual" encuentra
 * "Atención integral a víctimas de violencia sexual".
 */
class ImportacionCapacitadosService
{
    /** Columnas esperadas en la plantilla, en orden. */
    public const COLUMNAS = ['nombre_completo', 'documento', 'correo', 'telefono', 'rh', 'cursos', 'modalidad'];

    private const CACHE_TTL_MINUTOS = 15;

    /**
     * Lee el archivo, valida cada fila y guarda el resultado en caché
     * para confirmarlo después sin reprocesar el archivo.
     */
    public function previsualizar(UploadedFile $archivo): array
    {
        $hoja = IOFactory::load($archivo->getRealPath())->getActiveSheet();
        $filasExcel = $hoja->toArray(null, true, true, false);

        if (empty($filasExcel)) {
            return ['token' => null, 'filas' => [], 'resumen' => $this->resumenVacio()];
        }

        $encabezados = $this->normalizarEncabezados(array_shift($filasExcel));
        $todosCursos = Curso::activos()->get();
        $cursosPorSlug = $todosCursos->keyBy(fn (Curso $c) => Str::slug($c->nombre));

        $filas = [];
        $numeroFila = 1;

        foreach ($filasExcel as $filaExcel) {
            $numeroFila++;
            $datos = $this->mapearFila($encabezados, $filaExcel);

            if ($this->filaVacia($datos)) {
                continue;
            }

            $filas[] = $this->procesarFila($numeroFila, $datos, $cursosPorSlug, $todosCursos);
        }

        $token = (string) Str::uuid();
        Cache::put("importacion_capacitados:{$token}", $filas, now()->addMinutes(self::CACHE_TTL_MINUTOS));

        return [
            'token' => $token,
            'filas' => $filas,
            'resumen' => $this->resumir($filas),
        ];
    }

    /**
     * Crea/actualiza capacitados y solicitudes de certificación para las
     * filas seleccionadas de una previsualización ya guardada en caché.
     */
    public function confirmar(string $token, array $filasSeleccionadas): array
    {
        $filas = Cache::get("importacion_capacitados:{$token}");

        if ($filas === null) {
            throw new \RuntimeException('La previsualización expiró, vuelve a subir el archivo.');
        }

        $seleccionadas = array_map('intval', $filasSeleccionadas);

        $contadores = [
            'creados'            => 0,
            'actualizados'       => 0,
            'solicitudes_creadas' => 0,
            'omitidos'           => 0,
        ];

        DB::transaction(function () use ($filas, $seleccionadas, &$contadores) {
            foreach ($filas as $fila) {
                if (!empty($fila['errores']) || !in_array($fila['fila'], $seleccionadas, true)) {
                    $contadores['omitidos']++;
                    continue;
                }

                $datos = $fila['datos'];

                $capacitado = Capacitado::updateOrCreate(
                    ['documento' => $datos['documento']],
                    array_filter([
                        'nombre_completo' => $datos['nombre_completo'],
                        'correo'          => $datos['correo'] ?: null,
                        'telefono'        => $datos['telefono'] ?: null,
                        'rh'              => $datos['rh'] ?: null,
                    ], fn ($v) => $v !== null)
                );

                $contadores[$fila['accion'] === 'crear' ? 'creados' : 'actualizados']++;

                foreach ($fila['cursos'] as $curso) {
                    SolicitudCertificado::firstOrCreate(
                        [
                            'capacitado_id' => $capacitado->id,
                            'curso_id'      => $curso['id'],
                            'estado'        => 'pendiente',
                        ],
                        [
                            'modalidad' => $datos['modalidad'] ?: null,
                            'origen'    => 'importacion_excel',
                        ]
                    );

                    $contadores['solicitudes_creadas']++;
                }
            }
        });

        Cache::forget("importacion_capacitados:{$token}");

        return $contadores;
    }

    /**
     * Genera la plantilla Excel con encabezados, fila de ejemplo
     * y segunda hoja con los cursos disponibles.
     */
    public function generarPlantilla(): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Capacitados');
        $hoja->fromArray(self::COLUMNAS, null, 'A1');
        $hoja->fromArray([
            'Juan Pérez Gómez', '1234567890', 'juan@correo.com', '3001234567', 'O+',
            'Trabajo en alturas, SST básico',
            'virtual',
        ], null, 'A2');

        foreach (range('A', 'G') as $col) {
            $hoja->getColumnDimension($col)->setAutoSize(true);
        }

        $hojaCursos = $spreadsheet->createSheet();
        $hojaCursos->setTitle('Cursos disponibles');
        $hojaCursos->fromArray(['nombre'], null, 'A1');
        $hojaCursos->fromArray(
            Curso::activos()->orderBy('nombre')->pluck('nombre')->map(fn ($n) => [$n])->toArray(),
            null,
            'A2'
        );
        $hojaCursos->getColumnDimension('A')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    // ─────────────────────────────────────────────────────────────
    // Métodos privados
    // ─────────────────────────────────────────────────────────────

    private function normalizarEncabezados(array $fila): array
    {
        return array_map(
            fn ($v) => Str::of((string) $v)->lower()->trim()->ascii()->snake()->toString(),
            $fila
        );
    }

    private function mapearFila(array $encabezados, array $filaExcel): array
    {
        $datos = [];

        foreach ($encabezados as $indice => $encabezado) {
            if ($encabezado === '') {
                continue;
            }
            $datos[$encabezado] = trim((string) ($filaExcel[$indice] ?? ''));
        }

        foreach (self::COLUMNAS as $columna) {
            $datos[$columna] ??= '';
        }

        return $datos;
    }

    private function filaVacia(array $datos): bool
    {
        return $datos['nombre_completo'] === '' && $datos['documento'] === '';
    }

    /**
     * Valida una fila, resuelve los cursos (con búsqueda parcial)
     * y determina la acción (crear / actualizar).
     */
    private function procesarFila(
        int $numeroFila,
        array $datos,
        EloquentCollection $cursosPorSlug,
        EloquentCollection $todosCursos
    ): array {
        $errores = [];

        if ($datos['nombre_completo'] === '') {
            $errores[] = 'El nombre completo es requerido.';
        }

        if ($datos['documento'] === '') {
            $errores[] = 'El documento es requerido.';
        } elseif (strlen($datos['documento']) < 4 || strlen($datos['documento']) > 50) {
            $errores[] = 'El documento debe tener entre 4 y 50 caracteres.';
        }

        if ($datos['correo'] !== '' && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido.';
        }

        $modalidad = strtolower($datos['modalidad']);
        $datos['modalidad'] = in_array($modalidad, ['virtual', 'presencial'], true) ? $modalidad : '';

        // Resolver cursos (acepta varios separados por coma)
        $cursosEncontrados   = [];
        $cursosNoEncontrados = [];

        $textosCursos = array_filter(
            array_map('trim', explode(',', $datos['cursos'])),
            fn ($t) => $t !== ''
        );

        foreach ($textosCursos as $texto) {
            $curso = $this->buscarCurso($texto, $cursosPorSlug, $todosCursos);

            if ($curso !== null) {
                $cursosEncontrados[] = ['id' => $curso->id, 'nombre' => $curso->nombre, 'texto' => $texto];
            } else {
                $cursosNoEncontrados[] = $texto;
            }
        }

        $accion = 'crear';

        if ($datos['documento'] !== '' && empty($errores)) {
            $accion = Capacitado::porDocumento($datos['documento']) ? 'actualizar' : 'crear';
        }

        return [
            'fila'                 => $numeroFila,
            'datos'                => $datos,
            'accion'               => $accion,
            'cursos'               => $cursosEncontrados,
            'cursos_no_encontrados' => $cursosNoEncontrados,
            'errores'              => $errores,
        ];
    }

    /**
     * Busca un curso primero por slug exacto, luego por coincidencia
     * parcial: todas las palabras del texto deben aparecer en el slug
     * del nombre del curso. Devuelve el curso con nombre más corto
     * (más específico) si hay varios candidatos.
     */
    private function buscarCurso(
        string $texto,
        EloquentCollection $cursosPorSlug,
        EloquentCollection $todosCursos
    ): ?Curso {
        if ($texto === '') {
            return null;
        }

        $slug = Str::slug($texto);

        // 1. Coincidencia exacta por slug
        if ($cursosPorSlug->has($slug)) {
            return $cursosPorSlug->get($slug);
        }

        // 2. Coincidencia parcial: todas las palabras del texto están en el nombre del curso
        $palabras = array_filter(explode('-', $slug), fn ($p) => strlen($p) >= 3);

        if (empty($palabras)) {
            return null;
        }

        return $todosCursos
            ->filter(function (Curso $curso) use ($palabras) {
                $slugCurso = Str::slug($curso->nombre);
                foreach ($palabras as $palabra) {
                    if (!str_contains($slugCurso, $palabra)) {
                        return false;
                    }
                }
                return true;
            })
            ->sortBy(fn (Curso $c) => strlen($c->nombre))
            ->first();
    }

    private function resumir(array $filas): array
    {
        $resumen = $this->resumenVacio();

        foreach ($filas as $fila) {
            if (!empty($fila['errores'])) {
                $resumen['errores']++;
                continue;
            }

            $resumen[$fila['accion'] === 'crear' ? 'crear' : 'actualizar']++;

            if (empty($fila['cursos'])) {
                $resumen['sin_curso']++;
            }
        }

        $resumen['total'] = count($filas);

        return $resumen;
    }

    private function resumenVacio(): array
    {
        return ['total' => 0, 'crear' => 0, 'actualizar' => 0, 'sin_curso' => 0, 'errores' => 0];
    }
}
