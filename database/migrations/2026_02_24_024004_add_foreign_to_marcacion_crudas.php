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
        Schema::table('marcacion_crudas', function (Blueprint $table) {
            $table->foreign('empleado_id')
                ->references('id')
                ->on('empleados')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marcacion_crudas', function (Blueprint $table) {
            //
        });
    }
};
