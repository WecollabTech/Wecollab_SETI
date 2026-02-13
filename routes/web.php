<?php

use App\Http\Controllers\EstimacionController;
use App\Http\Controllers\FaseController;
use App\Http\Controllers\IntegrationesController;
use App\Http\Controllers\NivelComplejidadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TipoImplementacionController;
use App\Models\Integrationes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // 👇 Si el usuario está autenticado, mándalo al dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // 👇 Si NO está autenticado, muestra Welcome
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
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
});






//RUTA DE LISTA DE INTEGRACIONES
Route::middleware('auth')->group(function () {
    //Ruta de la lista
    Route::get('/integraciones', function () {
        return Inertia::render('Integraciones/Index');
    })->name('integraciones.index');


    //Ruta de creacion de Integraciones
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




});




//RUTAS DE FASES 
Route::middleware('auth')->group(function () {
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



});



//RUTAS DE TAREAS
Route::middleware('auth')->group(function () {

    //RUTAS DE LISTA DE TAREAS
    Route::get(
        '/tareas',
        fn() =>
        Inertia::render('Tareas/TareaList')
    )->name('tareas.index');

    //RUTAS PARA CREAR TAREAS 
    // CREAR TAREA
    Route::get(
        '/tareas/create',
        fn() =>
        Inertia::render('Tareas/TareaCrud')
    )->name('tareas.create');

    //RUTA PARA EDITAR TAREA
    Route::get(
        '/tareas/{tarea}/edit',
        fn($tarea) =>
        Inertia::render('Tareas/TareaEdit', ['tareaId' => $tarea])
    )->name('tareas.edit');


    //RUTA PARA VER DETALLE DE LA TAREA 
    Route::get('/tareas/{tarea}', [TareaController::class, 'Apishow'])->name('tareas.show');


    //Rutas Integraciones Tareas 
    Route::get('/integraciones_tarea', function () {
        return Inertia::render('IntegracionesTareas/IntegracionesTareasCrud');
    })->name('integraciones_tarea.index');

});


//RUTAS DE ESTIMACIONES 
Route::middleware('auth')->group(function () {

    //RUTA DE LISTA DE ESTIMACIONES 
    Route::get('/estimacion', function () {
        return Inertia::render('Estimacion/CreateEstimacion');
    })->name('estimacion');

    //RUTAS PARA CREAR ESTIMACIONES 
    Route::get('/estimaciones', function () {
        return Inertia::render('Estimacion/Index');
    })->name('estimaciones.listas');


    //RUTA PARA VER DETALLE DE LA ESTIMACIONES
    Route::get('/estimaciones/{estimacion}', function ($estimacion) {
        return Inertia::render('Estimacion/Show', [
            'estimacionId' => $estimacion
        ]);
    })->name('estimaciones.show');


    Route::get('/estimaciones/{estimacion}/pdf', [EstimacionController::class, 'exportarPdf'])
        ->name('estimaciones.pdf');


});

//RUTAS DE NIVEL DE COMPLEJIDAD
Route::middleware('auth')->group(function () {

    //RUTA DE LSITA DE NIVEL DE COMPLEJIDAD
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

});

//Rutas del vista y componentes de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/perfil', function () {
        return Inertia::render('Profile/DetallesPerfil');
    })->name('perfil');
});

require __DIR__ . '/auth.php';