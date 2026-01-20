<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimacionTarea extends Model
{
    use HasFactory;

    protected $table = 'estimacion_tareas';
    protected $fillable = ['estimacion_fase_id','titulo','duracion_minuto'];

}