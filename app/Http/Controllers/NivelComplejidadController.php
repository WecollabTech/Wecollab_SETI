<?php

namespace App\Http\Controllers;

use App\Models\NivelComplejidad;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NivelComplejidadController extends Controller
{
    /**
     * Listar todos los niveles
     */
    public function index()
    {
        $niveles = NivelComplejidad::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $niveles->items(),
            'current_page' => $niveles->currentPage(),
            'last_page' => $niveles->lastPage(),
        ]);
    }

    /**
     * Mostrar un nivel específico
     */
    public function show(NivelComplejidad $nivel)
    {
        return response()->json([
            'success' => true,
            'data' => $nivel,
        ]);
    }

    /**
     * Crear un nuevo nivel
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'factor' => 'required|numeric',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
        ]);

        $data = $request->only(['nombre', 'factor', 'descripcion', 'estado']);
        $nivel = NivelComplejidad::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Nivel registrado correctamente',
            'data' => $nivel,
        ]);
    }

    /**
     * Actualizar un nivel
     */
    public function update(Request $request, NivelComplejidad $nivel)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'factor' => 'required|numeric',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
        ]);

        $data = $request->only(['nombre', 'factor', 'descripcion', 'estado']);
        $nivel->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Nivel actualizado correctamente',
            'data' => $nivel,
        ]);
    }

    /**
     * Eliminar un nivel
     */
    public function destroy(NivelComplejidad $nivel)
    {
        $nivel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nivel eliminado correctamente',
        ]);
    }


    public function showView(NivelComplejidad $nivel)
{
    // Retorna la vista Inertia con el nivel
    return Inertia::render('NivelComplejidad/NivelShow', [
        'nivelId' => $nivel->id,
    ]);
}





}