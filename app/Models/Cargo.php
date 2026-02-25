<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_id'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}