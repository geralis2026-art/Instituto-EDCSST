<?php

namespace App\Services;

use App\Models\Capacitado;
use App\Models\Curso;
use App\Models\SolicitudCertificado;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Procesa la importación masiva de capacitados desde un archivo Excel.
 *
 * Por cada fila crea o actualiza un Capacitado (por documento) y, si el
 * curso indicado coincide con uno activo, registra una SolicitudCertificado
 * pendiente para la generación masiva de certificados (Fase 2b).
 */
class ImportacionCapacitadosService
{
    /** Columnas esperadas en la plantilla, en orden. */
    public const COLUMNAS = ['nombre_completo', 'documento', 'correo', 'telefono', 'rh', 'curso', 'modalidad'];

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
        $cursosPorSlug = Curso::activos()->get()->keyBy(fn (Curso $curso) => Str::slug($curso->nombre));

        $filas = [];
        $numeroFila = 1; // la fila 1 del Excel es el encabezado

        foreach ($filasExcel as $filaExcel) {
            $numeroFila++;

            $datos = $this->mapearFila($encabezados, $filaExcel);

            if ($this->filaVacia($datos)) {
                continue;
            }

            $filas[] = $this->procesarFila($numeroFila, $datos, $cursosPorSlug);
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
            'creados' => 0,
            'actualizados' => 0,
            'solicitudes_creadas' => 0,
            'omitidos' => 0,
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
                        'correo' => $datos['correo'] ?: null,
                        'telefono' => $datos['telefono'] ?: null,
                        'rh' => $datos['rh'] ?: null,
                    ], fn ($valor) => $valor !== null)
                );

                $contadores[$fila['accion'] === 'crear' ? 'creados' : 'actualizados']++;

                if ($fila['curso_id'] !== null) {
                    SolicitudCertificado::create([
                        'capacitado_id' => $capacitado->id,
                        'curso_id' => $fila['curso_id'],
                        'curso_texto' => $datos['curso'] ?: null,
                        'modalidad' => $datos['modalidad'] ?: null,
                        'estado' => 'pendiente',
                        'origen' => 'importacion_excel',
                    ]);

                    $contadores['solicitudes_creadas']++;
                }
            }
        });

        Cache::forget("importacion_capacitados:{$token}");

        return $contadores;
    }

    /**
     * Genera la plantilla Excel de importación con encabezados, una fila
     * de ejemplo y una segunda hoja con los nombres de cursos disponibles.
     */
    public function generarPlantilla(): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Capacitados');
        $hoja->fromArray(self::COLUMNAS, null, 'A1');
        $hoja->fromArray([
            'Juan Pérez Gómez', '1234567890', 'juan@correo.com', '3001234567', 'O+', 'Trabajo en alturas', 'virtual',
        ], null, 'A2');

        foreach (range('A', 'G') as $columna) {
            $hoja->getColumnDimension($columna)->setAutoSize(true);
        }

        $hojaCursos = $spreadsheet->createSheet();
        $hojaCursos->setTitle('Cursos disponibles');
        $hojaCursos->fromArray(['nombre'], null, 'A1');
        $hojaCursos->fromArray(
            Curso::activos()->orderBy('nombre')->pluck('nombre')->map(fn ($nombre) => [$nombre])->toArray(),
            null,
            'A2'
        );
        $hojaCursos->getColumnDimension('A')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Normaliza los encabezados del Excel a snake_case sin acentos para
     * que coincidan con las columnas esperadas sin importar mayúsculas
     * o espacios extra.
     */
    private function normalizarEncabezados(array $fila): array
    {
        return array_map(
            fn ($valor) => Str::of((string) $valor)->lower()->trim()->ascii()->snake()->toString(),
            $fila
        );
    }

    /** Combina los encabezados con los valores de una fila por posición. */
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

    /** Valida una fila y determina la acción a realizar y el curso a vincular. */
    private function procesarFila(int $numeroFila, array $datos, $cursosPorSlug): array
    {
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

        $cursoId = null;
        $cursoEncontrado = null;

        if ($datos['curso'] !== '') {
            $cursoEncontrado = $cursosPorSlug->get(Str::slug($datos['curso']));
            $cursoId = $cursoEncontrado?->id;
        }

        $accion = 'crear';

        if ($datos['documento'] !== '' && empty($errores)) {
            $accion = Capacitado::porDocumento($datos['documento']) ? 'actualizar' : 'crear';
        }

        return [
            'fila' => $numeroFila,
            'datos' => $datos,
            'accion' => $accion,
            'curso_id' => $cursoId,
            'curso_nombre' => $cursoEncontrado?->nombre,
            'errores' => $errores,
        ];
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

            if ($fila['curso_id'] === null) {
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
