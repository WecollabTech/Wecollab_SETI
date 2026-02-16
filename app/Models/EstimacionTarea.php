<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimacionTarea extends Model
{
    use HasFactory;

    protected $table = 'estimacion_tareas';
    protected $fillable = ['estimacion_fase_id', 'nombre_tarea', 'duracion_minuto'];


    //Relacion que tiene  con la estimacin 

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class);
    }



    //relacion que tendra con la fase
    public function fase()
    {
        return $this->belongsTo(Fase::class);
    }


    //Estimacion de fase relacionado con faseestimacion
    // Relación con la fase de la estimación
    public function estimacionFase()
    {
        return $this->belongsTo(EstimacionFase::class, 'estimacion_fase_id');
    }
}