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
        'observaciones',
        'tipo_e1_id', // Antes: tipo_entrada_1_id
        'tipo_s1_id', // Antes: tipo_salida_1_id
        'tipo_e2_id', // Antes: tipo_entrada_2_id
        'tipo_s2_id'  // Antes: tipo_salida_2_id
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    // Relación principal
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Relaciones con el catálogo de tipos de tickeo
     */

    public function tipoEntrada1()
    {
        return $this->belongsTo(TipoTickeo::class, 'tipo_e1_id');
    }

    public function tipoSalida1()
    {
        return $this->belongsTo(TipoTickeo::class, 'tipo_s1_id');
    }

    public function tipoEntrada2()
    {
        return $this->belongsTo(TipoTickeo::class, 'tipo_e2_id');
    }

    public function tipoSalida2()
    {
        return $this->belongsTo(TipoTickeo::class, 'tipo_s2_id');
    }
}