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
        Schema::create('excepcion_empleados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->cascadeOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->integer('minutos_extra_entrada')->default(0);
            $table->integer('minutos_extra_salida')->default(0);

            $table->string('motivo', 255)->nullable();

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
        Schema::dropIfExists('excepcion_empleados');
    }
};
