<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'horas_semanales'
    ];

    public function turnos()
    {
        return $this->hasMany(HorarioTurno::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionHorario::class);
    }
}