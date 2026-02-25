<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcepcionEmpleado extends Model
{
    protected $table = 'excepcion_empleados';

    protected $fillable = [
        'empleado_id',
        'fecha_inicio',
        'fecha_fin',
        'minutos_extra_entrada',
        'minutos_extra_salida',
        'motivo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}