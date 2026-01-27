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


    public function tareas()
    {
        return $this->hasMany(
            IntegracionTarea::class,
            'integracion_id'
        );
    }

    // Relación uno a muchos con IntegracionTareas
    public function integracionTareas()
    {
        return $this->hasMany(IntegracionTarea::class, 'integracion_id', 'id');
    }



    //Relacion de estimacion con integraciones 
    public function estimaciones()
    {
        return $this->hasMany(EstimacionIntegracion::class);
    }

}