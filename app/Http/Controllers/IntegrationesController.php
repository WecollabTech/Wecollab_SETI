<?php

namespace App\Http\Controllers;


use App\Models\Integrationes;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Trae todas las integraciones activas (o todas)
        $integraciones = Integrationes::select('id', 'nombre', 'descripcion')->get();

        return response()->json([
            'success' => true,
            'data' => $integraciones,
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
                'descripcion.required' => 'La descripción es obligatoria.',
            ]
        );

        $integracion = Integrationes::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Integración creada correctamente',
            'data' => $integracion,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Integrationes $integracion)
    {
        // Si viene desde API
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $integracion,
            ]);
        }

        // Vista Inertia
        return Inertia::render('Integraciones/IntegracionShow', [
            'integracion' => $integracion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IntegrationesController $integratione)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $integracion = Integrationes::findOrFail($id);

        $data = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
                'descripcion.required' => 'La descripción es obligatoria.',
            ]
        );

        $integracion->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Integración actualizada correctamente',
            'data' => $integracion,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Integrationes $integratione)
    {
        $integratione->delete();

        return response()->json([
            'success' => true,
            'message' => 'Integración eliminada correctamente',
        ]);
    }




    public function duplicar($id)
    {
        $integracion = Integrationes::with('tareas')->findOrFail($id);

        // Duplicar integración
        $nueva = $integracion->replicate();
        $nueva->nombre = $integracion->nombre . ' (Copia)';
        $nueva->save();

        // Duplicar tareas
        foreach ($integracion->tareas as $tarea) {
            $nuevaTarea = $tarea->replicate();
            $nuevaTarea->integracion_id = $nueva->id;
            $nuevaTarea->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Integración duplicada correctamente',
            'data' => $nueva
        ]);
    }



}