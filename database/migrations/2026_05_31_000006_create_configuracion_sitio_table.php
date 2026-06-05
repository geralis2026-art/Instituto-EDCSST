<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configuración general del sitio
     * Esta tabla solo tiene UNA fila (se actualiza, no se crean más registros)
     * Almacena datos generales: contactos, redes, logo, plantilla certificado
     */
    public function up(): void
    {
        Schema::create('configuracion_sitio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_instituto')->default('Instituto EDCSST');
            $table->text('descripcion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo_contacto')->nullable();
            $table->string('direccion')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('logo')->nullable();
            $table->string('plantilla_certificado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_sitio');
    }
};
