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
        Schema::create('horario_turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')
                ->constrained('horarios')
                ->cascadeOnDelete();

            $table->tinyInteger('dia_semana'); // 1=Lunes ... 7=Domingo
            $table->tinyInteger('numero_turno'); // 1 o 2

            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->integer('minutos_tolerancia')->default(0);

            $table->timestamps();

            $table->index(['horario_id','dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horario_turno');
    }
};
