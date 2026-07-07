<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->string('tipo_documento', 4)->default('CC')->after('nombre_completo');
        });
    }

    public function down(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->dropColumn('tipo_documento');
        });
    }
};
