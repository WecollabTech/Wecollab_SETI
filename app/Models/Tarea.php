<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarea extends Model
{

    use HasFactory;
    protected $table = 'tareas';
    protected $fillable = [
        'titulo',
        'id_proceso',
        'descripcion',
        'estado',
        'activo',
        'duracion_minuto',
        'orden',
        'fase_id',
    ];


    //RELACION DE LA TABLA FASES CON TAREAS DE MUCHOS A MUCHOS
    public function fase()
    {
        return $this->belongsTo(Fase::class, 'fase_id');
    }



}