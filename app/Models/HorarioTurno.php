<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioTurno extends Model
{
    protected $table = 'horario_turnos';

    protected $fillable = [
        'horario_id',
        'dia_semana',
        'numero_turno',
        'hora_inicio',
        'hora_fin',
        'minutos_tolerancia'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i'
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}