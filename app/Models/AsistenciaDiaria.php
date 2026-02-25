<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaDiaria extends Model
{
    protected $table = 'asistencia_diarias';

    protected $fillable = [
        'empleado_id',
        'fecha',

        'entrada_1_prog',
        'salida_1_prog',
        'entrada_1_real',
        'salida_1_real',

        'entrada_2_prog',
        'salida_2_prog',
        'entrada_2_real',
        'salida_2_real',

        'minutos_tarde',
        'minutos_extra',
        'estado_dia',
        'tipo_registro',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}