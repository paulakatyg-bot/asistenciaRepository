<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcacionCruda extends Model
{
    protected $table = 'marcacion_crudas';

    protected $fillable = [
        'empleado_id',
        'fecha_hora',
        'id_dispositivo',
        'codigo_estado',
        'tipo_evento',
        'flag',
        'archivo_origen',
        'procesado'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'procesado' => 'boolean'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}