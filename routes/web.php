<?php

use App\Http\Controllers\EstimacionController;
use App\Http\Controllers\FaseController;
use App\Http\Controllers\IntegrationesController;
use App\Http\Controllers\NivelComplejidadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TipoImplementacionController;
use App\Models\Integrationes;
use App\Models\Tarea;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



// Vistas de Tipo Implementación
Route::prefix('tipoimplementacion')->group(function () {

    Route::get('/', function () {
        return Inertia::render('TipoImplementacion/Index');
    })->name('tipoimplementacion.index');

    Route::get('/create', function () {
        return Inertia::render('TipoImplementacion/Create');
    })->name('tipoimplementacion.create');

    Route::get('/{id}/edit', [TipoImplementacionController::class, 'edit'])
        ->name('tipoimplementacion.edit');

    Route::get('/{id}/show', [TipoImplementacionController::class, 'show'])
        ->name('tipoimplementacion.show');
});




//Rutas de Integraciones 
Route::get('/integraciones', function () {
    return Inertia::render('Integraciones/Index');
})->name('integraciones.index');

Route::get('/integraciones/create', function () {
    return Inertia::render('Integraciones/Create');
})->name('integraciones.create');

Route::get('/integraciones/{integracion}/edit', function ($integracionId) {
    return Inertia::render('Integraciones/IntegracionEdit', [
        'integracionId' => $integracionId
    ]);
})->name('integraciones.edit');

Route::get('/integraciones/{integracion}', function (Integrationes $integracion) {
    return Inertia::render('Integraciones/IntegracionShow', [
        'integracion' => $integracion->load('tiposImplementacion'),
    ]);
})->name('integraciones.show');


//Rutas Integraciones Tareas 
Route::get('/integraciones_tarea', function () {
    return Inertia::render('IntegracionesTareas/IntegracionesTareasCrud');
})->name('integraciones_tarea.index');



Route::get('/fases', function () {
    return Inertia::render('Fases/Prueba');
})->name('fases.prueba');











// Listar fases 
Route::get('/fases', function () {
    return Inertia::render('Fases/Faselist');
})->name('fases.index');

// Crear nueva fase
Route::get('/fases/create', function () {
    return Inertia::render('Fases/FaseCrud');
})->name('fases.create');

// Editar fase
Route::get('/fases/{fase}/edit', function ($faseId) {
    return Inertia::render('Fases/FaseEdit', [
        'faseId' => $faseId
    ]);
})->name('fases.edit');

// Ver detalle de fases
Route::get('/fases/{fase}', [FaseController::class, 'show'])
    ->name('fases.show');



//RUTAS DE VISTAS DE TARRAS WEB.PHP
Route::get(
    '/tareas',
    fn() =>
    Inertia::render('Tareas/TareaList')
)->name('tareas.index');

Route::get(
    '/tareas/create',
    fn() =>
    Inertia::render('Tareas/TareaCrud')
)->name('tareas.create');

Route::get(
    '/tareas/{tarea}/edit',
    fn($tarea) =>
    Inertia::render('Tareas/TareaEdit', ['tareaId' => $tarea])
)->name('tareas.edit');

Route::get('/tareas/{tarea}', [TareaController::class, 'Apishow'])->name('tareas.show'); // <-- esta es la clave








//Rutas de Estimaciones para las vistas 
Route::get('/estimacion', function () {
    return Inertia::render('Estimacion/CreateEstimacion');
})->name('estimacion');


Route::get('/estimaciones', function () {
    return Inertia::render('Estimacion/Index');
})->name('estimaciones.listas');

Route::get('/estimaciones/{estimacion}', function ($estimacion) {
    return Inertia::render('Estimacion/Show', [
        'estimacionId' => $estimacion
    ]);
})->name('estimaciones.show');

Route::get('/estimaciones/{estimacion}/pdf', [EstimacionController::class, 'exportarPdf'])
    ->name('estimaciones.pdf');




//RUTAS PARA EL NIVEL DE COMPLEJIDAD
Route::get('/niveles', function () {
    return Inertia::render('NivelComplejidad/NivelesList');
})->name('niveles.index');

Route::get('/niveles/create', function () {
    return Inertia::render('NivelComplejidad/NivelCreate');
})->name('niveles.create');

Route::get('/niveles/{nivel}/edit', function ($nivel) {
    return Inertia::render('NivelComplejidad/NivelEdit', [
        'nivelId' => $nivel
    ]);
})->name('niveles.edit');
Route::get('/niveles/{nivel}/show', [NivelComplejidadController::class, 'showView'])
    ->name('niveles.show');















Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/perfil', function () {
    return Inertia::render('Profile/DetallesPerfil');
})->name('perfil');






Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';