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
public function store(Request $request)
{
    $data = $request->validate([
        'titulo' => 'required|string|max:255',
        'id_proceso' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'estado' => 'required|string|max:50',
        'activo' => 'boolean',
        'duracion_minuto' => 'nullable|integer|min:1',
        'orden' => 'nullable|integer|min:1',
        'fase_id' => 'required|exists:fases,id',
    ]);

    Tarea::create($data);

    return response()->json([
        'message' => 'Tarea creada correctamente',
    ], 201);
}


    //FUNCION PARA VER DETALLES DE LAS TAREAS
    public function show(Tarea $tarea) {
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
public function update(Request $request, Tarea $tarea)
{
    $data = $request->validate([
        'titulo' => 'required|string|max:255',
        'id_proceso' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'estado' => 'required|string|max:50',
        'activo' => 'boolean',
        'duracion_minuto' => 'nullable|integer|min:1',
        'orden' => 'nullable|integer|min:1',
        'fase_id' => 'required|exists:fases,id',
    ]);

    $tarea->update($data);

    return response()->json([
        'message' => 'Tarea actualizada correctamente',
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