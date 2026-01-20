<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoImplementacion extends Model
{
    use HasFactory;

    // Nombre real de la tabla
    protected $table = 'tipo_implementacion';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nombre',
        'descripcion',
        'rubrica',
        'estado',
        
    ];

    // Casts (opcional, pero recomendado)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // TipoImplementacion.php
// Relación muchos a muchos con integraciones
    public function integraciones()
    {
        return $this->belongsToMany(
            Integrationes::class,                   // Modelo correcto
            'integracion_tipoimplementacion',     // Tabla pivot
            'tipo_implementacion_id',             // FK de este modelo en la tabla pivot
            'integracion_id'                      // FK del modelo relacionado
        );
    }

    // Relación muchos a muchos con fases
    public function fases()
        {
            return $this->belongsToMany(
                Fase::class,
                'fase_tipo_implementacion',
                'tipo_implementacion_id',
                'fase_id'
            );
        }


// Relación uno a muchos con estimaciones
    public function estimaciones()
    {
        return $this->hasMany(Estimacion::class);
    }
    

}