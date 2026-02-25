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
          Schema::table('empleados', function (Blueprint $table) {
                $table->string('codigo_biometrico')
                    ->nullable()
                    ->after('id');
            });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('codigo_biometrico');
        });
    }
};
