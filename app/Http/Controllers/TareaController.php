<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TareaController extends Controller
{


    //FUNCION PARA LISTAR TARAS CON FASE
    public function index(Request $request)
    {
        return Tarea::with('fase')
            ->when($request->search, function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->search}%");
            })
            ->paginate(5);
    }


    //FUNCION PARA CREAR Y GUARDAR TAREA EN LA BASE DE DATOS.
    // public function store(Request $request)
    // {
    //     // Mensajes de validación personalizados
    //     $messages = [
    //         'titulo.required' => 'El título de la tarea es obligatorio.',
    //         'titulo.string' => 'El título debe ser un texto válido.',
    //         'titulo.min' => 'El título debe tener al menos 15 caracteres.',
    //         'titulo.max' => 'El título no puede superar 255 caracteres.',

    //         'id_proceso.required' => 'El ID de proceso es obligatorio.',
    //         'id_proceso.string' => 'El ID de proceso debe ser un texto válido.',
    //         'id_proceso.max' => 'El ID de proceso no puede superar 255 caracteres.',

    //         'descripcion.string' => 'La descripción debe ser un texto válido.',

    //         'estado.required' => 'El estado de la tarea es obligatorio.',
    //         'estado.string' => 'El estado debe ser un texto válido.',
    //         'estado.max' => 'El estado no puede superar 50 caracteres.',

    //         'activo.boolean' => 'El campo activo debe ser verdadero o falso.',

    //         'duracion_minuto.required' => 'Debes indicar la duración de la tarea en minutos.',
    //         'duracion_minuto.integer' => 'La duración debe ser un número válido.',
    //         'duracion_minuto.min' => 'La duración mínima permitida es de 1 minuto.',

    //         'orden.integer' => 'El orden debe ser un número válido.',
    //         'orden.min' => 'El orden mínimo permitido es 1.',

    //         'fase_id.required' => 'Debes seleccionar una fase para la tarea.',
    //         'fase_id.exists' => 'La fase seleccionada no existe en el sistema.',
    //     ];

    //     // Validación con regla de mínimo y máximo para titulo
    //     $data = $request->validate([
    //         'titulo' => 'required|string|min:5|max:255',
    //         'id_proceso' => 'required|string|max:255',
    //         'descripcion' => 'nullable|string',
    //         'estado' => 'nullable|string|max:50',
    //         'activo' => 'boolean',
    //         'duracion_minuto' => 'required|integer|min:1',
    //         'orden' => 'nullable|integer|min:1',
    //         'fase_id' => 'required|exists:fases,id',
    //     ], $messages);

    //     // Crear la tarea
    //     $tarea = Tarea::create($data);

    //     return response()->json([
    //         'message' => 'Tarea creada correctamente ✅',
    //         'success' => true,
    //         'data' => $tarea,
    //     ], 201);
    // }
    public function store(Request $request)
    {
        // Mensajes de validación personalizados
        $messages = [
            'titulo.required' => 'El título de la tarea es obligatorio.',
            'titulo.string' => 'El título debe ser un texto válido.',
            'titulo.min' => 'El título debe tener al menos 5 caracteres.',
            'titulo.max' => 'El título no puede superar 255 caracteres.',

            'id_proceso.required' => 'El ID de proceso es obligatorio.',
            'id_proceso.string' => 'El ID de proceso debe ser un texto válido.',
            'id_proceso.max' => 'El ID de proceso no puede superar 255 caracteres.',

            'descripcion.string' => 'La descripción debe ser un texto válido.',

            'estado.string' => 'El estado debe ser un texto válido.',
            'estado.max' => 'El estado no puede superar 50 caracteres.',

            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',

            'duracion_minuto.integer' => 'La duración debe ser un número válido.',
            'duracion_minuto.min' => 'La duración mínima permitida es 0 minutos.',

            'orden.integer' => 'El orden debe ser un número válido.',
            'orden.min' => 'El orden mínimo permitido es 1.',

            'fase_id.required' => 'Debes seleccionar una fase para la tarea.',
            'fase_id.exists' => 'La fase seleccionada no existe en el sistema.',
        ];

        // Validación
        $data = $request->validate([
            'titulo' => 'required|string|min:5|max:255',
            'id_proceso' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string|max:50',
            'activo' => 'boolean',
            'duracion_minuto' => 'nullable|integer|min:0',
            'orden' => 'nullable|integer|min:1',
            'fase_id' => 'required|exists:fases,id',
        ], $messages);

        // 🔐 Asegurar duración mínima
        $data['duracion_minuto'] = $data['duracion_minuto'] ?? 0;

        // Crear la tarea
        $tarea = Tarea::create($data);

        return response()->json([
            'message' => 'Tarea creada correctamente ✅',
            'success' => true,
            'data' => $tarea,
        ], 201);
    }





    //FUNCION PARA VER DETALLES DE LAS TAREAS
    public function show(Tarea $tarea)
    {
        return response()->json($tarea);
    }


    public function Apishow(Tarea $tarea)
    {
        // Cargar relaciones necesarias
        $tarea->load('fase'); // subtareas si existen

        return Inertia::render('Tareas/TareaShow', [
            'tarea' => $tarea
        ]);
    }


    //FUNCION PARA ACTUALIZAR TAREAS
    // public function update(Request $request, Tarea $tarea)
    // {
    //     $data = $request->validate([
    //         'titulo' => 'required|string|max:255',
    //         'id_proceso' => 'required|string|max:255',
    //         'descripcion' => 'nullable|string',
    //         'estado' => 'required|string|max:50',
    //         'activo' => 'boolean',
    //         'duracion_minuto' => 'nullable|integer|min:1',
    //         'orden' => 'nullable|integer|min:1',
    //         'fase_id' => 'required|exists:fases,id',
    //     ]);

    //     $tarea->update($data);

    //     return response()->json([
    //         'message' => 'Tarea actualizada correctamente',
    //     ]);
    // }



    public function update(Request $request, Tarea $tarea)
    {
        // Mensajes personalizados (opcional pero recomendado)
        $messages = [
            'titulo.required' => 'El título de la tarea es obligatorio.',
            'titulo.string' => 'El título debe ser un texto válido.',
            'titulo.max' => 'El título no puede superar 255 caracteres.',

            'id_proceso.required' => 'El ID de proceso es obligatorio.',
            'id_proceso.string' => 'El ID de proceso debe ser un texto válido.',
            'id_proceso.max' => 'El ID de proceso no puede superar 255 caracteres.',

            'descripcion.string' => 'La descripción debe ser un texto válido.',

            'estado.string' => 'El estado debe ser un texto válido.',
            'estado.max' => 'El estado no puede superar 50 caracteres.',

            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',

            'duracion_minuto.integer' => 'La duración debe ser un número válido.',
            'duracion_minuto.min' => 'La duración mínima permitida es 0 minutos.',

            'orden.integer' => 'El orden debe ser un número válido.',
            'orden.min' => 'El orden mínimo permitido es 1.',

            'fase_id.required' => 'Debes seleccionar una fase para la tarea.',
            'fase_id.exists' => 'La fase seleccionada no existe en el sistema.',
        ];

        // Validación
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'id_proceso' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string|max:50',
            'activo' => 'boolean',
            'duracion_minuto' => 'nullable|integer|min:0',
            'orden' => 'nullable|integer|min:1',
            'fase_id' => 'required|exists:fases,id',
        ], $messages);

        // 🔐 Forzar duración mínima
        $data['duracion_minuto'] = $data['duracion_minuto'] ?? 0;

        // Actualizar tarea
        $tarea->update($data);

        return response()->json([
            'message' => 'Tarea actualizada correctamente ✅',
            'success' => true,
            'data' => $tarea,
        ]);
    }



    public function destroy(Tarea $tarea)
    {
        $tarea->delete();
        return response()->json(['success' => true]);
    }













    public function create()
    {
        //
    }



    public function edit(Tarea $tarea)
    {
        //
    }

}