<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimacion extends Model
{
    use HasFactory;
    protected $table = 'estimaciones'; // Nombre exacto de tu tabla
    protected $fillable = [
        'tipo_implementacion_id',
        'nombre_tipo_implementacion',
        'complejidad_id',
        'total_minutos',
        'total_horas',
        'comentarios',
        // 🔹 Nuevos campos
        'nombre_empresa',
        'responsable',
        'id_negocio',
    ];

    //Tipos de Implementacio que estara relacionada a cada estimacion
    public function tipoImplementacion()
    {
        return $this->belongsTo(TipoImplementacion::class);
    }

    // El Nivel de complejidad que estara relacionado a cada estimacion
    public function complejidad()
    {
        return $this->belongsTo(NivelComplejidad::class, 'complejidad_id');
    }


    //Relacion con la fase de las implementaciones 
    public function fases()
    {
        return $this->hasMany(EstimacionFase::class);
    }

    //Tareaes relacionadas a cada estimacion 
    public function tareas()
    {
        return $this->hasMany(EstimacionTarea::class);
    }

    //Integaciones que estara Realacionado a cada estimacion
    public function integraciones()
    {
        return $this->hasMany(EstimacionIntegracion::class);
    }

}