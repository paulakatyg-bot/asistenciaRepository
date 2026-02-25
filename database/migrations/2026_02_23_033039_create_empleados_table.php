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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('ci', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->enum('genero', ['M','F','OTRO']);
            $table->date('fecha_nacimiento');
            $table->string('direccion', 255)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->date('fecha_contratacion');
            $table->boolean('estado')->default(true);

            $table->foreignId('grupo_beneficio_id')
                ->nullable()
                ->constrained('grupo_beneficios')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['estado']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empleados');
    }
};
