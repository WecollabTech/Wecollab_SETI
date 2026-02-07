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




    // Guardar nueva implementación
    public function store(Request $request)
    {
        try {
            // ✅ Validación backend
            $data = $request->validate([
                'nombre' => 'required|string|min:3|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'rubrica' => 'nullable|string|max:255',
                'alcance' => 'nullable|string|min:10|max:1000', // 👈 NUEVO
                'activo' => 'required|boolean',

                'integraciones' => 'nullable|array',
                'integraciones.*' => 'exists:integraciones,id',

                'fases' => 'nullable|array',
                'fases.*' => 'exists:fases,id',
            ], [
                'nombre.required' => 'El nombre de la implementación es obligatorio.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'alcance.min' => 'El alcance debe tener al menos 10 caracteres.',
                'alcance.max' => 'El alcance no puede superar los 1000 caracteres.',

                'activo.required' => 'Debes seleccionar el estado.',

                'integraciones.array' => 'Las integraciones deben ser un arreglo válido.',
                'integraciones.*.exists' => 'Una de las integraciones seleccionadas no es válida.',

                'fases.array' => 'Las fases deben ser un arreglo válido.',
                'fases.*.exists' => 'Una de las fases seleccionadas no es válida.',
            ]);

            // ✅ Crear Tipo de Implementación
            $tipo = TipoImplementacion::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'rubrica' => $data['rubrica'] ?? null,
                'alcance' => $data['alcance'] ?? null, // 👈 NUEVO
                'estado' => $data['activo'],
            ]);

            // ✅ Asociar integraciones (si hay)
            if (!empty($data['integraciones'])) {
                $tipo->integraciones()->sync($data['integraciones']);
            }

            // ✅ Asociar fases (si hay)
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
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar.',
                'error' => $e->getMessage(),
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
        try {
            // 🔎 Buscar el registro
            $tipo = TipoImplementacion::findOrFail($id);

            // ✅ Validación backend
            $data = $request->validate([
                'nombre' => 'required|string|min:3|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'rubrica' => 'nullable|string|max:255',
                'alcance' => 'nullable|string|min:10|max:1000', // 👈 ALCANCE
                'activo' => 'required|boolean',

                'integraciones' => 'nullable|array',
                'integraciones.*' => 'exists:integraciones,id',

                'fases' => 'nullable|array',
                'fases.*' => 'exists:fases,id',
            ], [
                'nombre.required' => 'El nombre de la implementación es obligatorio.',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',

                'alcance.min' => 'El alcance debe tener al menos 10 caracteres.',
                'alcance.max' => 'El alcance no puede superar los 1000 caracteres.',

                'activo.required' => 'Debes seleccionar el estado.',

                'integraciones.array' => 'Las integraciones deben ser un arreglo válido.',
                'integraciones.*.exists' => 'Una de las integraciones seleccionadas no es válida.',

                'fases.array' => 'Las fases deben ser un arreglo válido.',
                'fases.*.exists' => 'Una de las fases seleccionadas no es válida.',
            ]);

            // 📝 Actualizar datos principales
            $tipo->update([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'rubrica' => $data['rubrica'] ?? null,
                'alcance' => $data['alcance'] ?? null, // 👈 ALCANCE
                'estado' => $data['activo'],
            ]);

            // 🔗 Sincronizar integraciones
            if (array_key_exists('integraciones', $data)) {
                $tipo->integraciones()->sync($data['integraciones'] ?? []);
            }

            // 🔗 Sincronizar fases
            if (array_key_exists('fases', $data)) {
                $tipo->fases()->sync($data['fases'] ?? []);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tipo de implementación actualizado correctamente',
                'data' => $tipo,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hay errores en el formulario',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El tipo de implementación no existe.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar.',
                'error' => $e->getMessage(),
            ], 500);
        }
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