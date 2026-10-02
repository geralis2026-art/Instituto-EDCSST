<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prueba de la autorización para el tratamiento de datos personales (Ley 1581 de 2012, art. 9;
 * Decreto 1377 de 2013). Se guarda cuándo se aceptó y qué versión de la política estaba vigente.
 * Quedan en NULL los registros anteriores y los creados por el personal (la autorización se
 * recoge por otro medio).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['capacitados', 'mensajes'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->timestamp('autorizacion_datos_at')->nullable();
                $table->string('autorizacion_datos_version', 20)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['capacitados', 'mensajes'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn(['autorizacion_datos_at', 'autorizacion_datos_version']);
            });
        }
    }
};
