<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }
}