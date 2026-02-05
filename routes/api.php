<?php

use App\Http\Controllers\EstimacionController;
use App\Http\Controllers\FaseController;
use App\Http\Controllers\IntegracionTareaController;
use App\Http\Controllers\IntegrationesController;
use App\Http\Controllers\NivelComplejidadController;
use App\Http\Controllers\TareaController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TipoImplementacionController;
use App\Models\User;

//RUTAS DE PROYECTO DE IMPLEMENTACIO

Route::get('/tipoimplementacion', [TipoImplementacionController::class, 'index']);
Route::post('/tipoimplementacion', [TipoImplementacionController::class, 'store']);

//Para Filtrar Integracion por tipo de Implementacion
Route::get('/tipoimplementacion/{id}/integraciones', [TipoImplementacionController::class, 'integraciones']);
Route::get(
    '/tipoimplementacion/{id}/fases',
    [TipoImplementacionController::class, 'fases']
);
Route::put('/tipoimplementacion/{id}', [TipoImplementacionController::class, 'update']);
Route::delete('/tipoimplementacion/{id}', [TipoImplementacionController::class, 'destroy']);




//rutas de taras integraciones 
Route::post('/integraciones_tareas', [IntegracionTareaController::class, 'store']);

//Rutas de Integraciones

Route::get('/integraciones', [IntegrationesController::class, 'index']);
Route::post('/integraciones', [IntegrationesController::class, 'store']);
Route::put('/integraciones/{integracion}', [IntegrationesController::class, 'update']);
// Obtener una fase por ID
Route::get('/integraciones/{integracion}', [IntegrationesController::class, 'show']);
Route::delete('/integraciones/{integratione}', [IntegrationesController::class, 'destroy']);
Route::post(
    '/integraciones/{id}/duplicar',
    [IntegrationesController::class, 'duplicar']
)->name('integraciones.duplicar');








//rutas Fases

Route::get('/fases', [FaseController::class, 'index']);
Route::post('/fases', [FaseController::class, 'store']);
Route::put('/fases/{fase}', [FaseController::class, 'update']);
// Obtener una fase por ID
Route::get('/fases/{fase}', [FaseController::class, 'show']);
Route::get('/tipoimplementacion/{tipo}/fases', [TipoImplementacionController::class, 'fases']);
Route::get(
    '/tipoimplementacion/{tipo}/integraciones-tareas',
    [TipoImplementacionController::class, 'integracionesConTareas']
);
//Ruta para Obtener toda las fases 
Route::get('/listafases', [FaseController::class, 'all']); // sin paginación




//RUTAS API DE LA TAREAS
Route::get('/tareas', [TareaController::class, 'index']);
Route::post('/tareas', [TareaController::class, 'store']);
Route::get('/tareas/{tarea}', [TareaController::class, 'show']);
Route::put('/tareas/{tarea}', [TareaController::class, 'update']);
Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy']);



//RUTAS PARA NIVEL DE COMPLEJIDAD.
Route::get('/niveles', [NivelComplejidadController::class, 'index']);
Route::get('/niveles/{nivel}', [NivelComplejidadController::class, 'show']);
Route::post('/niveles', [NivelComplejidadController::class, 'store']);
Route::put('/niveles/{nivel}', [NivelComplejidadController::class, 'update']);
Route::delete('/niveles/{nivel}', [NivelComplejidadController::class, 'destroy']);
Route::get('/nivel-complejidad', [NivelComplejidadController::class, 'apiindex']);



//RUTAS PARA GUARDAR ESTIMACION DE PROYECTOS
Route::post('/estimaciones', [EstimacionController::class, 'store']);
Route::get('/estimaciones', [EstimacionController::class, 'index']);
Route::get('/estimaciones/{estimacion}', [EstimacionController::class, 'show']);
Route::delete('/estimaciones/{estimacion}', [EstimacionController::class, 'destroy']);

Route::post(
    '/estimaciones/{estimacion}/crear-proyecto-bitrix',
    [EstimacionController::class, 'crearProyecto']
);


//Ruta para cargar usuarios 
Route::get('/usuarios', function () {
    return response()->json(
        User::select('id', 'name', 'email')->orderBy('name')->get()
    );
});