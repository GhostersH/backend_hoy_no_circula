<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    // Campos asignables en masa
    protected $fillable = [
        'placa',
        'color',
        'modelo',
        'chasis',
    ];



    public $timestamps = true;
}
