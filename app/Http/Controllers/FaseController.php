<?php

namespace App\Http\Controllers;

use App\Models\Fase;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Listar todas las fases
    public function index(Request $request)
    {
        $query = Fase::query();

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                ->orWhere('descripcion', 'like', '%' . $request->search . '%');
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validar columnas permitidas
        $allowedColumns = ['id', 'nombre', 'descripcion'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortDirection);

        $fases = $query->paginate(5);

        return response()->json($fases);
    }

    public function all()
    {
        return response()->json(
            Fase::orderBy('id', 'asc')->get()
        );
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
    // Crear fase

    public function store(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'minutos_base' => 'nullable|integer|min:0',
            ],
            [
                'nombre.required' => 'El nombre de la fase es obligatorio.',
                'nombre.string' => 'El nombre debe ser un texto válido.',
                'nombre.max' => 'El nombre no puede superar los 255 caracteres.',

                'descripcion.string' => 'La descripción debe ser un texto válido.',

                'minutos_base.integer' => 'Los minutos base deben ser un número entero.',
                'minutos_base.min' => 'Los minutos base no pueden ser negativos.',
            ]
        );

        $data = $request->only(['nombre', 'descripcion', 'minutos_base']);

        $fase = Fase::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Fase registrada correctamente',
            'data' => $fase,
        ]);
    }











    /**
     * Display the specified resource.
     */
    // Mostrar fase específica
    public function show(Fase $fase)
    {
        // Si es llamada API
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $fase,
            ]);
        }

        // Si es vista Inertia
        return Inertia::render('Fases/FaseShow', [
            'fase' => $fase,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fase $fase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Actualizar fase
    public function update(Request $request, Fase $fase)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'minutos_base' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['nombre', 'descripcion', 'minutos_base']);
        $fase->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Fase actualizada correctamente',
            'data' => $fase,
        ]);
    }












    /**
     * Remove the specified resource from storage.
     */
    // Eliminar fase
    public function destroy(Fase $fase)
    {
        $fase->delete();
        return response()->json(['message' => 'Fase eliminada']);
    }














    public function duplicate($id)
    {
        try {
            // Cargar fase con sus relaciones (si existen)
            $fase = Fase::findOrFail($id);

            // Duplicar fase
            $nueva = $fase->replicate();
            $nueva->nombre = $fase->nombre . ' (Copia)';
            $nueva->save();

            // Si la fase tiene relaciones, duplicarlas aquí
            // Ejemplo si tuviera tareas:
            // foreach ($fase->tareas as $tarea) {
            //     $nuevaTarea = $tarea->replicate();
            //     $nuevaTarea->fase_id = $nueva->id;
            //     $nuevaTarea->save();
            // }

            return response()->json([
                'success' => true,
                'message' => 'Fase duplicada correctamente',
                'data' => $nueva
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fase no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al duplicar la fase'
            ], 500);
        }
    }


}