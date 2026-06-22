<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->dropIndex('capacitados_documento_index');
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->dropIndex('certificados_codigo_unico_index');
        });
    }

    public function down(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->index('documento');
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->index('codigo_unico');
        });
    }
};
