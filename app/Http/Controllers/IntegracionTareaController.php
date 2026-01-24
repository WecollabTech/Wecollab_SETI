<?php

namespace App\Http\Controllers;
use App\Models\IntegracionTarea;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class IntegracionTareaController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'titulo' => 'required|string|min:3',
                'id_proceso' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'activo' => 'boolean',
                'duracion_minuto' => 'nullable|integer|min:0',
                'orden' => 'nullable|integer|min:1',
                'integracion_id' => 'required|exists:integraciones,id',
            ],
            [
                // TITULO
                'titulo.required' => 'El título de la tarea es obligatorio.',
                'titulo.string' => 'El título debe ser un texto válido.',
                'titulo.min' => 'El título debe tener al menos :min caracteres.',

                // ID PROCESO
                'id_proceso.string' => 'El ID del proceso debe ser un texto.',
                'id_proceso.max' => 'El ID del proceso no puede exceder :max caracteres.',

                // DESCRIPCIÓN
                'descripcion.string' => 'La descripción debe ser un texto válido.',

                // ACTIVO
                'activo.boolean' => 'El estado activo debe ser verdadero o falso.',

                // DURACIÓN
                'duracion_minuto.integer' => 'La duración debe ser un número entero.',
                'duracion_minuto.min' => 'La duración no puede ser negativa.',

                // ORDEN
                'orden.integer' => 'El orden debe ser un número entero.',
                'orden.min' => 'El orden debe ser mayor o igual a :min.',

                // INTEGRACIÓN
                'integracion_id.required' => 'Debes seleccionar una integración.',
                'integracion_id.exists' => 'La integración seleccionada no es válida.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tarea = IntegracionTarea::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tarea de integración creada correctamente.',
            'data' => $tarea,
        ]);
    }


    public function duplicar($id)
    {
        $tarea = IntegracionTarea::findOrFail($id);

        $nueva = $tarea->replicate(); // clona todo
        $nueva->titulo = $tarea->titulo . ' (Copia)';
        $nueva->activo = false; // opcional
        $nueva->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarea duplicada correctamente',
            'data' => $nueva,
        ]);
    }





}