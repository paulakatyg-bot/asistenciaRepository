<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoTickeo extends Model
{
    use HasFactory;

    protected $table = 'tipo_tickeos';

    protected $fillable = [
        'nombre',
        'color',
        'requiere_observacion'
    ];

    /**
     * Relación inversa: Un tipo de tickeo puede estar en muchas asistencias.
     * Esto es útil si alguna vez quieres saber cuántas "Comisiones" hubo en el mes.
     */
    public function asistenciasEntrada1()
    {
        return $this->hasMany(AsistenciaDiaria::class, 'tipo_entrada_1_id');
    }

    // ... Se pueden añadir las demás relaciones (salida_1, entrada_2, etc.) si es necesario.
}