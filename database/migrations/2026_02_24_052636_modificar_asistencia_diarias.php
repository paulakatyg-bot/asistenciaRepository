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

            $table->dropColumn([
                'hora_entrada_programada',
                'hora_salida_programada',
                'hora_entrada_real',
                'hora_salida_real'
            ]);

            $table->time('entrada_1_prog')->nullable();
            $table->time('salida_1_prog')->nullable();
            $table->time('entrada_1_real')->nullable();
            $table->time('salida_1_real')->nullable();

            $table->time('entrada_2_prog')->nullable();
            $table->time('salida_2_prog')->nullable();
            $table->time('entrada_2_real')->nullable();
            $table->time('salida_2_real')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
