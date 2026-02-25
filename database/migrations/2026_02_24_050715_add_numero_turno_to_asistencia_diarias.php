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
         Schema::table('asistencia_diarias', function (Blueprint $table) {
            $table->tinyInteger('numero_turno')->default(1)->after('fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asistencia_diarias', function (Blueprint $table) {
            $table->dropColumn('numero_turno');
        });
    }
};
