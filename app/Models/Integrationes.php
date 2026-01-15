<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Integrationes extends Model
{
     use HasFactory;
     protected $table = 'integraciones';
     protected $fillable = ['nombre', 'descripcion'];





     // Integracion.php
public function tiposImplementacion()
{
    return $this->belongsToMany(
        TipoImplementacion::class,
        'integracion_tipoimplementacion',
        'integracion_id',
        'tipo_implementacion_id'
    );
}








}