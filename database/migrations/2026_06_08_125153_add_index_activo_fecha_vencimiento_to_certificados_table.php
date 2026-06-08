<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->index(['activo', 'fecha_vencimiento'], 'certificados_activo_fecha_vencimiento_index');
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropIndex('certificados_activo_fecha_vencimiento_index');
        });
    }
};
