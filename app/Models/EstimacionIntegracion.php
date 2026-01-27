<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimacionIntegracion extends Model
{
    protected $table = 'estimacion_integraciones';
    protected $fillable = [
        'estimacion_id',
        'integracion_id',
    ];

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class);
    }


    public function integracion()
    {
        return $this->belongsTo(Integrationes::class, 'integracion_id');
    }

    public function tareas()
    {
        return $this->hasMany(
            EstimacionIntegracionTarea::class,
            'estimacion_integracion_id'
        );
    }


}