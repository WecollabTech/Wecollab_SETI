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
        'comentarios'
    ];

    public function fases() {
        return $this->hasMany(EstimacionFase::class);
    }

   


}