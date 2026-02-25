<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('asistencia_diarias', function (Blueprint $table) {
            // Definimos el tipo de registro: MAQUINA (biométrico) o MANUAL (regularización)
            $table->enum('tipo_registro', ['MAQUINA', 'MANUAL'])
                  ->default('MAQUINA')
                  ->after('estado_dia');
        });
    }

    public function down()
    {
        Schema::table('asistencia_diarias', function (Blueprint $table) {
            $table->dropColumn('tipo_registro');
        });
    }
};