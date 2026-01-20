<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimacion;
use App\Models\EstimacionFase;
use App\Models\EstimacionTarea;
use Illuminate\Support\Facades\DB;

class EstimacionController extends Controller
{
    public function store(Request $request)
    {
        // Validar datos principales y de fases/tareas
        $request->validate([
            'tipo_implementacion_id' => 'required|exists:tipo_implementacion,id',
            'nombre_tipo_implementacion' => 'required|string|max:255',
            'complejidad_id' => 'required|exists:nivel_complejidads,id',
            'fases' => 'required|array|min:1',
            'fases.*.fase_id' => 'required|exists:fases,id',
            'fases.*.tareas' => 'required|array|min:1',
            'fases.*.tareas.*.titulo' => 'required|string',
            'fases.*.tareas.*.duracion_minuto' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcular totales dinámicamente
            $totalMinutos = 0;
            $totalHoras = 0.0;

            foreach ($request->fases as $fase) {
                $faseMinutos = collect($fase['tareas'])->sum('duracion_minuto');
                $totalMinutos += $faseMinutos;
                $totalHoras += $faseMinutos / 60;
            }

            // Crear estimación
            $estimacion = Estimacion::create([
                'tipo_implementacion_id' => $request->tipo_implementacion_id,
                'nombre_tipo_implementacion' => $request->nombre_tipo_implementacion,
                'complejidad_id' => $request->complejidad_id,
                'total_minutos' => $totalMinutos,
                'total_horas' => $totalHoras,
                'comentarios' => $request->comentarios ?? null,
            ]);

            // Crear fases y tareas
            foreach ($request->fases as $fase) {
                $faseMinutos = collect($fase['tareas'])->sum('duracion_minuto');
                $faseHoras = $faseMinutos / 60;

                $estimacionFase = EstimacionFase::create([
                    'estimacion_id' => $estimacion->id,
                    'fase_id' => $fase['fase_id'],
                    'minutos' => $faseMinutos,
                    'horas' => $faseHoras,
                ]);

                foreach ($fase['tareas'] as $tarea) {
                    EstimacionTarea::create([
                        'estimacion_fase_id' => $estimacionFase->id,
                        'titulo' => $tarea['titulo'],
                        'duracion_minuto' => $tarea['duracion_minuto'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Estimación creada correctamente',
                'estimacion' => $estimacion
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al guardar la estimación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}