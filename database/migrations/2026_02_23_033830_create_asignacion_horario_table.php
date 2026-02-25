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
        Schema::create('asignacion_horarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->cascadeOnDelete();

            $table->foreignId('horario_id')
                ->constrained('horarios')
                ->cascadeOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            $table->timestamps();

            $table->index(['empleado_id','fecha_inicio','fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asignacion_horarios');
    }
};
