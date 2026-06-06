<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['admin', 'capacitador'])->default('admin')->after('activo');
        });

        // Usuarios existentes sin rol → admin por defecto
        DB::table('users')->whereNull('rol')->update(['rol' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rol');
        });
    }
};
