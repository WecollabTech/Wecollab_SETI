<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
        use HasFactory;
         protected $table = 'fases';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'minutos_base',
    ];


    // Una fase tiene muchas tareas
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'fase_id');
    }


     // Opcional: relación inversa con tipo implementación
    public function tiposImplementacion()
    {
        return $this->belongsToMany(
            TipoImplementacion::class,
            'fase_tipo_implementacion',
            'fase_id',
            'tipo_implementacion_id'
        );
    }





}