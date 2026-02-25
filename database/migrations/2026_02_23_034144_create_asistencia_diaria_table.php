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
        Schema::create('asistencia_diarias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->cascadeOnDelete();

            $table->date('fecha');

            $table->time('hora_entrada_programada')->nullable();
            $table->time('hora_salida_programada')->nullable();

            $table->time('hora_entrada_real')->nullable();
            $table->time('hora_salida_real')->nullable();

            $table->integer('minutos_tarde')->default(0);
            $table->integer('minutos_extra')->default(0);

            $table->enum('estado_dia', [
                'NORMAL',
                'TARDE',
                'FERIADO',
                'INASISTENCIA',
                'JUSTIFICADO',
                'FIN DE SEMANA'
            ])->default('NORMAL');

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(['empleado_id','fecha']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asistencia_diarias');
    }
};
