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
        Schema::create('tipo_tickeos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Comisión, Permiso, Olvido, etc.
            $table->string('color')->default('secondary'); // Para mostrar badges de colores
            $table->boolean('requiere_observacion')->default(false);
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
        Schema::dropIfExists('tipo_tickeos');
    }
};
