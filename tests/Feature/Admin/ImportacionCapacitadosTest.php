<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportacionCapacitadosTest extends TestCase
{
    use RefreshDatabase;

    private function archivoExcel(array $filas): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->fromArray(['nombre_completo', 'tipo_documento', 'documento', 'correo', 'telefono', 'rh', 'cursos', 'modalidad'], null, 'A1');
        $hoja->fromArray($filas, null, 'A2');

        $ruta = tempnam(sys_get_temp_dir(), 'import_test_').'.xlsx';
        (new Xlsx($spreadsheet))->save($ruta);

        return new UploadedFile($ruta, 'capacitados.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    /**
     * Cubre el flujo completo (subir → previsualizar → confirmar) por HTTP.
     * Antes de este fix, CapacitadoImportConfirmarRequest exigía
     * 'size:40' para el token, pero Str::uuid() genera 36 caracteres:
     * la confirmación fallaba siempre con "El token de sesión no es
     * válido", para cualquier usuario, sin excepción.
     */
    public function test_flujo_completo_de_importacion_crea_capacitado_y_solicitud(): void
    {
        $admin = User::factory()->admin()->create();
        $curso = Curso::factory()->create(['nombre' => 'SST básico', 'activo' => true]);

        $archivo = $this->archivoExcel([
            ['Juan Pérez', 'CC', '900111222', 'juan@correo.com', '3001112233', 'O+', 'SST básico', 'virtual'],
        ]);

        $previa = $this->actingAs($admin)->post('/admin/capacitados-importar', [
            'archivo_excel' => $archivo,
        ]);

        $previa->assertOk();
        $token = $previa->viewData('token');
        $this->assertNotNull($token);
        $this->assertSame(36, strlen($token), 'El token generado por Str::uuid() debe tener 36 caracteres.');

        $numeroFila = $previa->viewData('filas')[0]['fila'];

        $confirmar = $this->actingAs($admin)->post('/admin/capacitados-importar/confirmar', [
            'token' => $token,
            'filas' => [$numeroFila],
        ]);

        $confirmar->assertSessionDoesntHaveErrors();
        $confirmar->assertRedirect();

        $this->assertDatabaseHas('capacitados', ['documento' => '900111222']);
        $this->assertDatabaseHas('solicitudes_certificado', [
            'curso_id'  => $curso->id,
            'modalidad' => 'virtual',
        ]);
    }

    public function test_confirmar_con_token_invalido_devuelve_error_de_validacion(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/capacitados-importar/confirmar', [
            'token' => 'no-es-un-uuid',
            'filas' => [1],
        ])->assertSessionHasErrors('token');
    }
}
