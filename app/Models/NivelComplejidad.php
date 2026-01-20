<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelComplejidad extends Model
{
    use HasFactory;
    protected $table = 'nivel_complejidads';
    protected $fillable = [
        'nombre', 
        'factor', 
        'descripcion', 
        'estado',
        'rubricas'
        ];





        public function estimaciones()
    {
        return $this->hasMany(Estimacion::class, 'complejidad_id');
    }
}