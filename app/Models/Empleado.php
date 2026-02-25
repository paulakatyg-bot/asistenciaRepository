<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'ci',
        'nombres',
        'apellidos',
        'genero',
        'fecha_nacimiento',
        'direccion',
        'celular',
        'email',
        'fecha_contratacion',
        'estado',
        'grupo_beneficio_id',
        'codigo_biometrico'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_contratacion' => 'date',
        'estado' => 'boolean'
    ];

    public function grupoBeneficio()
    {
        return $this->belongsTo(GrupoBeneficio::class);
    }

    public function asignacionesHorarios()
    {
        return $this->hasMany(AsignacionHorario::class);
    }

    public function excepciones()
    {
        return $this->hasMany(ExcepcionEmpleado::class);
    }

    public function marcaciones()
    {
        return $this->hasMany(MarcacionCruda::class);
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaDiaria::class);
    }
}