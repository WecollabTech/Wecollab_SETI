<?php

namespace App\Http\Controllers;

use App\Models\Fase;
use App\Models\TipoImplementacion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TipoImplementacionController extends Controller
{

/**
     * Listar tipos de implementación
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => TipoImplementacion::all()
        ]);
    }

    public function data()
    {
        return response()->json(
            TipoImplementacion::all()
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
    public function store(Request $request)
    {
        return TipoImplementacion::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoImplementacion $id)
    {
        return TipoImplementacion::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoImplementacion $tipoImplementacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoImplementacion $id)
    {
          $item = TipoImplementacion::findOrFail($id);
        $item->update($request->all());
        return $item;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoImplementacion $tipoImplementacion)
    {
        //
    }







    
}