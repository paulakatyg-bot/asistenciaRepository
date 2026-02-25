<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoBeneficio extends Model
{
    protected $table = 'grupo_beneficios';

    protected $fillable = [
        'nombre',
        'minutos_tolerancia_extra_entrada',
        'minutos_tolerancia_extra_salida',
        'descripcion'
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}