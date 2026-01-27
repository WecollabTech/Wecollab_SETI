<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimacionIntegracionTarea extends Model
{
    protected $table = 'estimacion_integracion_tareas';

    protected $fillable = [
        'estimacion_integracion_id',
        'nombre_tarea_integracion',
        'duracion_estimada_minutos',
    ];

    public function estimacionIntegracion()
    {
        return $this->belongsTo(
            EstimacionIntegracion::class,
            'estimacion_integracion_id'
        );
    }

}