<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimacionFase extends Model
{
    use HasFactory;

    protected $table = 'estimacion_fases';
    protected $fillable = ['estimacion_id','fase_id','minutos','horas'];

    public function tareas() {
        return $this->hasMany(EstimacionTarea::class);
    }


}