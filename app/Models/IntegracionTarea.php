<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegracionTarea extends Model
{
    use HasFactory;
    protected $table = 'integracion_tareas';
    protected $fillable = [
        'integracion_id',
        'titulo',
        'id_proceso',
        'descripcion',
        'activo',
        'duracion_minuto',
        'url_contenido',
        'orden',
    ];




    public function integracion()
    {
        return $this->belongsTo(
            Integrationes::class,
            'integracion_id'
        );
    }


}