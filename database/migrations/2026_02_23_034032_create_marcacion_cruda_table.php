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
        Schema::create('marcacion_crudas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('empleado_id');
            $table->dateTime('fecha_hora');

            $table->integer('id_dispositivo')->nullable();
            $table->integer('codigo_estado')->nullable();
            $table->integer('tipo_evento')->nullable();
            $table->integer('flag')->nullable();

            $table->string('archivo_origen')->nullable();
            $table->boolean('procesado')->default(false);

            $table->timestamps();

            $table->index(['empleado_id','fecha_hora']);
            $table->index(['procesado']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marcacion_crudas');
    }
};
