<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    protected $table = 'calendarios';

    protected $primaryKey = 'fecha';

    public $incrementing = false; // porque no es autoincremental

    protected $keyType = 'string'; // las fechas se manejan como string internamente

    public $timestamps = false; // porque no tiene created_at ni updated_at

    protected $fillable = [
        'fecha',
        'tipo_dia',
        'descripcion'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];
}