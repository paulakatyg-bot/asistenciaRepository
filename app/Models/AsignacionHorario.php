<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionHorario extends Model
{
    protected $table = 'asignacion_horarios';

    protected $fillable = [
        'empleado_id',
        'horario_id',
        'fecha_inicio',
        'fecha_fin'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}