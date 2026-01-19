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
public function index()
{
    $tipos = Fase::orderBy('id', 'desc')->paginate(5);
    return response()->json($tipos);
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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'minutos_base' => 'nullable|integer|min:0',
        ]);

        // Solo tomamos los campos permitidos para evitar MassAssignmentException
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
}