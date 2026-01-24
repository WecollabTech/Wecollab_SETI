<?php

namespace App\Http\Controllers;
use App\Models\Fase;
use Inertia\Inertia;

use App\Models\TipoImplementacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;


class TipoImplementacionController extends Controller
{

    /**
     * Listar tipos de implementación
     */
    public function index()
    {
        $tipos = TipoImplementacion::orderBy('id', 'desc')->paginate(5);
        return response()->json($tipos);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    // Guardar nueva implementación
    public function store(Request $request)
    {
        try {
            // Validación backend
            $data = $request->validate([
                'nombre' => 'required|string|max:255|min:3',
                'descripcion' => 'nullable|string|max:1000',
                'rubrica' => 'nullable|string|max:255',
                'activo' => 'required|boolean',
                'integraciones' => 'nullable|array', // <-- validar array
                'integraciones.*' => 'exists:integraciones,id', // cada ID debe existir
                'fases' => 'nullable|array', // <-- validar array
                'fases.*' => 'exists:fases,id', // cada ID debe existir
            ], [
                'nombre.required' => 'El nombre de la implementación es obligatorio.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
                'activo.required' => 'Debes seleccionar el estado.',
                'integraciones.array' => 'Las integraciones deben ser un arreglo válido.',
                'integraciones.*.exists' => 'Una de las integraciones seleccionadas no es válida.',
                'fases.array' => 'Las integraciones deben ser un arreglo válido.',
                'fases.*.exists' => 'Una de las integraciones seleccionadas no es válida.',
            ]);

            // Crear Tipo de Implementación
            $tipo = TipoImplementacion::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'rubrica' => $data['rubrica'] ?? null,
                'estado' => $data['activo'],
            ]);

            // Asociar integraciones (si hay)
            if (!empty($data['integraciones'])) {
                $tipo->integraciones()->sync($data['integraciones']);
            }


            // Asociar Fases (si hay)
            if (!empty($data['fases'])) {
                $tipo->fases()->sync($data['fases']);
            }


            return response()->json([
                'success' => true,
                'message' => 'Tipo de implementación registrado correctamente',
                'data' => $tipo,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hay errores en el formulario',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar: ' . $e->getMessage(),
            ], 500);
        }
    }







    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tipo = TipoImplementacion::with(['integraciones', 'fases'])->findOrFail($id);

        return Inertia::render('TipoImplementacion/Show', [
            'tipo' => $tipo
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tipo = TipoImplementacion::with(['integraciones', 'fases'])->findOrFail($id);

        return Inertia::render('TipoImplementacion/Edit', [
            'tipo' => $tipo
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoImplementacion::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255|min:3',
            'descripcion' => 'nullable|string|max:1000',
            'rubrica' => 'nullable|string|max:255',
            'activo' => 'required|boolean',
            'integraciones' => 'nullable|array',
            'integraciones.*' => 'exists:integraciones,id',
            'fases' => 'nullable|array',
            'fases.*' => 'exists:fases,id',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'activo.required' => 'Debes seleccionar el estado.',
        ]);

        $tipo->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'rubrica' => $data['rubrica'] ?? null,
            'estado' => $data['activo'],
        ]);

        // Actualizar relaciones
        if (isset($data['integraciones'])) {
            $tipo->integraciones()->sync($data['integraciones']);
        }

        if (isset($data['fases'])) {
            $tipo->fases()->sync($data['fases']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tipo de implementación actualizado correctamente',
            'data' => $tipo,
        ]);
    }


    public function destroy($id)
    {
        $tipo = TipoImplementacion::findOrFail($id);

        // Eliminar relaciones si quieres limpiar la tabla pivote
        $tipo->integraciones()->detach();
        $tipo->fases()->detach();

        $tipo->delete();

        return response()->json([
            'success' => true,
            'message' => ' Tipo de implementación eliminado correctamente'
        ]);
    }









    //filtrar integraciones por tipo
    public function integraciones($id)
    {
        $tipo = TipoImplementacion::with('integraciones')->findOrFail($id);
        return response()->json([
            'data' => $tipo->integraciones
        ]);
    }




    public function fases($id)
    {
        try {
            // Cargar tipo de implementación con sus fases y las tareas de cada fase
            $tipo = TipoImplementacion::with(['fases.tareas'])->findOrFail($id);

            return response()->json([
                'data' => $tipo->fases
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function integracionesConTareas($id)
    {
        try {
            $tipo = TipoImplementacion::with([
                'integraciones.tareas'
            ])->findOrFail($id);

            return response()->json([
                'data' => $tipo->integraciones
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }




}