<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue certificados emitidos manualmente (flujo presencial actual)
     * de los generados automáticamente al aprobar el quiz del aula virtual.
     */
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->enum('origen', ['manual', 'virtual'])->default('manual')->after('curso_id');
            $table->foreignId('quiz_intento_id')->nullable()->after('origen')
                ->constrained('quiz_intentos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quiz_intento_id');
            $table->dropColumn('origen');
        });
    }
};
