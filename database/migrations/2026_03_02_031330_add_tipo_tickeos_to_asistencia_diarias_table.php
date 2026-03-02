<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asistencia_diarias', function (Blueprint $table) {
            // Añadimos los IDs que relacionan cada tickeo con un tipo
            $table->foreignId('tipo_e1_id')->nullable()->after('entrada_1_real')->constrained('tipo_tickeos')->nullOnDelete();
            $table->foreignId('tipo_s1_id')->nullable()->after('salida_1_real')->constrained('tipo_tickeos')->nullOnDelete();
            $table->foreignId('tipo_e2_id')->nullable()->after('entrada_2_real')->constrained('tipo_tickeos')->nullOnDelete();
            $table->foreignId('tipo_s2_id')->nullable()->after('salida_2_real')->constrained('tipo_tickeos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asistencia_diarias', function (Blueprint $table) {
            $table->dropForeign(['tipo_e1_id', 'tipo_s1_id', 'tipo_e2_id', 'tipo_s2_id']);
            $table->dropColumn(['tipo_e1_id', 'tipo_s1_id', 'tipo_e2_id', 'tipo_s2_id']);
        });
    }
};
