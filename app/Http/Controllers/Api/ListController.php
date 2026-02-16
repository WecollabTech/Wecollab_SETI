<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estimacion;
use Illuminate\Support\Facades\DB; // <-- IMPORTAR DB AQUÍ
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
        try {
            // Obtener todas las integraciones de estimaciones con sus relaciones
            $integraciones = \App\Models\EstimacionIntegracion::with('estimacion', 'integracion')
                ->select(
                    'id',
                    'estimacion_id',
                    'integracion_id',
                    'created_at',
                    'updated_at'
                )
                ->get();

            // Transformar los datos para el frontend
            $resultado = $integraciones->map(function ($integracion) {
                return [
                    'id' => $integracion->id,
                    'nombre' => $integracion->integracion?->nombre ?? 'Sin nombre',
                    'descripcion' => $integracion->integracion?->descripcion ?? '',
                    'estimacion_id' => $integracion->estimacion_id,
                    'estimacion_nombre' => $integracion->estimacion?->nombre_empresa ?? 'Sin estimación',
                    'estado' => 'activa', // Puedes agregar un campo estado si lo necesitas
                    'created_at' => $integracion->created_at,
                    'updated_at' => $integracion->updated_at
                ];
            });

            return response()->json($resultado);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
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
            'estado',
            'total_horas',
            'created_at'
        )->latest()->get();
    }


    public function tareasEstimaciones()
    {
        try {
            // Obtener todas las tareas con sus relaciones
            $tareas = \App\Models\EstimacionTarea::with(['estimacionFase.estimacion'])
                ->select(
                    'id',
                    'estimacion_fase_id',
                    'nombre_tarea as nombre', // Alias para que el frontend lo entienda
                    'duracion_minuto as horas', // Alias para que el frontend lo entienda
                    DB::raw("'pendiente' as estado"), // Estado por defecto
                    'created_at as fecha_entrega'
                )
                ->get();

            // Transformar los datos para el frontend
            $resultado = $tareas->map(function ($tarea) {
                // Obtener la estimación a través de la relación
                $estimacion = $tarea->estimacionFase?->estimacion;

                return [
                    'id' => $tarea->id,
                    'nombre' => $tarea->nombre ?? 'Sin nombre',
                    'descripcion' => $tarea->descripcion ?? '',
                    'horas' => $tarea->horas ?? 0,
                    'estado' => $estimacion?->estado ?? 'pendiente', // Usar el estado de la estimación
                    'fecha_entrega' => $tarea->fecha_entrega,
                    'estimacion_id' => $estimacion?->id, // Obtener el ID de la estimación
                    'estimacion_nombre' => $estimacion?->nombre_empresa ?? 'Sin estimación'
                ];
            });

            return response()->json($resultado);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}