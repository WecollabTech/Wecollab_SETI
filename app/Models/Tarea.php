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
        'url_contenido',
        'estado',
        'activo',
        'duracion_minuto',
        'orden',
        'fase_id',
    ];


    // Detecta el tipo de contenido automáticamente
    public function getTipoContenidoAttribute()
    {
        if (!$this->url_contenido)
            return null;

        if (str_contains($this->url_contenido, 'youtube') || str_contains($this->url_contenido, 'youtu.be')) {
            return 'youtube';
        }

        if (str_contains($this->url_contenido, 'loom')) {
            return 'loom';
        }

        if (str_contains($this->url_contenido, 'drive.google')) {
            return 'drive';
        }

        return 'archivo';
    }












    //RELACION DE LA TABLA FASES CON TAREAS DE MUCHOS A MUCHOS
    public function fase()
    {
        return $this->belongsTo(Fase::class, 'fase_id');
    }



}