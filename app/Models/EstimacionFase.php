<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimacionFase extends Model
{
    use HasFactory;

    protected $table = 'estimacion_fases';
    protected $fillable = ['estimacion_id', 'fase_id', 'minutos', 'horas'];

    //Fase que estara relacionada con cada estimacion 
    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class);
    }

    //Fase que estara asocida a la fase de estimacion public function fase
    public function fase()
    {
        return $this->belongsTo(Fase::class);
    }


    // Tareas calculadas dentro de esta fase
    // Relación con las tareas de esta fase
    public function tareas()
    {
        return $this->hasMany(EstimacionTarea::class, 'estimacion_fase_id');
    }
}