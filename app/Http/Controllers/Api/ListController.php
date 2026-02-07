<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estimacion;
use App\Models\Fase;
use App\Models\Integrationes;
use App\Models\NivelComplejidad;
use App\Models\Tarea;
use App\Models\TipoImplementacion;

class ListController extends Controller
{
    public function fases()
    {
        return Fase::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function integraciones()
    {
        return Integrationes::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function niveles()
    {
        return NivelComplejidad::select('id', 'nombre')->where('estado', 1)->get();
    }

    public function tareas()
    {
        return Tarea::select('id', 'titulo', 'fase_id')->where('activo', 1)->get();
    }

    public function tipoImplementacion()
    {
        return TipoImplementacion::select('id', 'nombre')->get();
    }

    public function estimaciones()
    {
        return Estimacion::select(
            'id',
            'nombre_empresa',
            'total_horas',
            'created_at'
        )->latest()->get();
    }

}