<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estimacion;
use App\Models\EstimacionFase;
use App\Models\EstimacionTarea;
use App\Models\EstimacionIntegracion;
use App\Models\EstimacionIntegracionTarea;

class EstimacionController extends Controller
{



    //funcion para listar estimaciones realizadas 

    public function index(Request $request)
    {
        $search = $request->search;

        $estimaciones = Estimacion::with([
            'tipoImplementacion:id,nombre',
            'complejidad:id,nombre'
        ])
            ->when($search, function ($query) use ($search) {
                $query->where('nombre_tipo_implementacion', 'like', "%{$search}%")
                    ->orWhereHas('tipoImplementacion', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($estimaciones);
    }



    //funcion para ver detalles de la estimacion 
    public function show($id)
    {
        $estimacion = Estimacion::with([
            'fases.fase',
            'fases.tareas',
            'integraciones.integracion',
            'complejidad',
            'integraciones.tareas', // 🔥 ahora sí apunta bien
        ])->findOrFail($id);

        return response()->json([
            'id' => $estimacion->id,
            'nombre_tipo_implementacion' => $estimacion->nombre_tipo_implementacion,
            'comentarios' => $estimacion->comentarios,
            'total_horas' => $estimacion->total_horas,
            'complejidad' => $estimacion->complejidad,

            'fases' => $estimacion->fases->map(fn($fase) => [
                'id' => $fase->fase->id,
                'nombre' => $fase->fase->nombre,
                'tareas' => $fase->tareas->map(fn($t) => [
                    'nombre_tarea' => $t->nombre_tarea,
                    'duracion_minuto' => $t->duracion_minuto,
                ]),
            ]),

            'integraciones' => $estimacion->integraciones->map(fn($i) => [
                'id' => $i->integracion->id,
                'nombre' => $i->integracion->nombre,
                'tareas' => $i->tareas->map(fn($t) => [
                    'nombre_tarea' => $t->nombre_tarea_integracion,
                    'duracion_minuto' => $t->duracion_estimada_minutos,
                ]),
            ]),
        ]);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1️⃣ Guardar estimación principal
            $estimacion = Estimacion::create([
                'tipo_implementacion_id' => $request->tipo_implementacion_id,
                'nombre_tipo_implementacion' => $request->nombre_tipo_implementacion,
                'complejidad_id' => $request->complejidad_id,
                'comentarios' => $request->comentarios,
                'total_horas' => $request->total_horas,
                'total_minutos' => round($request->total_horas * 60),
            ]);

            // 2️⃣ Guardar fases + tareas
            foreach ($request->fases ?? [] as $fase) {

                $totalMinutosFase = collect($fase['tareas'])
                    ->sum('duracion_minuto');

                $estimacionFase = EstimacionFase::create([
                    'estimacion_id' => $estimacion->id,
                    'fase_id' => $fase['referencia_id'],
                    'minutos' => $totalMinutosFase,
                    'horas' => round($totalMinutosFase / 60, 2),
                ]);

                foreach ($fase['tareas'] as $tarea) {
                    EstimacionTarea::create([
                        'estimacion_fase_id' => $estimacionFase->id,
                        'nombre_tarea' => $tarea['titulo'],
                        'duracion_minuto' => $tarea['duracion_minuto'],
                    ]);
                }
            }

            // 3️⃣ Guardar integraciones + tareas
            foreach ($request->integraciones ?? [] as $integracion) {

                $estimacionIntegracion = EstimacionIntegracion::create([
                    'estimacion_id' => $estimacion->id,
                    'integracion_id' => $integracion['referencia_id'],
                ]);

                foreach ($integracion['tareas'] as $tarea) {
                    EstimacionIntegracionTarea::create([
                        'estimacion_integracion_id' => $estimacionIntegracion->id,
                        'nombre_tarea_integracion' => $tarea['titulo'],
                        'duracion_estimada_minutos' => $tarea['duracion_minuto'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Estimación guardada correctamente',
                'estimacion_id' => $estimacion->id
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al guardar la estimación',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    //funcion para elimirar una estimacion 

    public function destroy(Estimacion $estimacion)
    {
        $estimacion->delete();

        return response()->json([
            'message' => 'Estimación eliminada correctamente'
        ]);
    }




    //funcion para Generar PDF de la estimacion 
    public function exportarPdf($id)
    {
        $estimacion = Estimacion::with([
            'fases.fase',
            'fases.tareas',
            'integraciones.integracion',
            'complejidad',
            'integraciones.tareas',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.estimacion', [
            'estimacion' => $estimacion
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('estimacion_' . $estimacion->id . '.pdf');
    }
}