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
        Schema::create('grupo_beneficios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->integer('minutos_tolerancia_extra_entrada')->default(0);
            $table->integer('minutos_tolerancia_extra_salida')->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_beneficios');
    }
};
