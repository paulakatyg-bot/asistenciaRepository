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
        // 1. Asegurar que la tabla de tipos existe
        if (!Schema::hasTable('tipo_tickeos')) {
            Schema::create('tipo_tickeos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('color')->default('secondary');
                $table->boolean('requiere_observacion')->default(false);
                $table->timestamps();
            });
        }

        // 2. Añadir las columnas solo si no existen
        Schema::table('asistencia_diarias', function (Blueprint $table) {
            $cols = ['tipo_e1_id', 'tipo_s1_id', 'tipo_e2_id', 'tipo_s2_id'];
            $after = ['entrada_1_real', 'salida_1_real', 'entrada_2_real', 'salida_2_real'];

            foreach ($cols as $key => $col) {
                if (!Schema::hasColumn('asistencia_diarias', $col)) {
                    $table->foreignId($col)->nullable()->after($after[$key])
                        ->constrained('tipo_tickeos')->nullOnDelete();
                }
            }
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
