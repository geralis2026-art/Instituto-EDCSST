<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NOT NULL explícito: la columna original (2026_06_06_100000) se
        // creó sin ->nullable(), y un MODIFY sin especificarlo la habría
        // dejado nullable por defecto en MySQL.
        DB::statement("ALTER TABLE users MODIFY rol ENUM('admin', 'capacitador', 'instructor') NOT NULL DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY rol ENUM('admin', 'capacitador') NOT NULL DEFAULT 'admin'");
    }
};
