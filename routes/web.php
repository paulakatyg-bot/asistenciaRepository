<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\GrupoBeneficioController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\HorarioTurnoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AsignacionHorarioController;
use App\Http\Controllers\ExcepcionEmpleadoController;
use App\Http\Controllers\MarcacionCrudaController;
use App\Http\Controllers\ProcesarAsistenciaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::resource('empleados', EmpleadoController::class)
        ->except(['show']);

});
Route::middleware(['auth'])->group(function () {

    Route::resource('grupo_beneficios', GrupoBeneficioController::class)
        ->except(['show']);

});

Route::middleware(['auth'])->group(function () {

    Route::resource('unidades', UnidadController::class)
        ->except(['show']);

});

Route::middleware(['auth'])->group(function () {

    Route::resource('cargos', CargoController::class)
        ->except(['show']);

});



Route::middleware(['auth'])->group(function () {

    Route::resource('horario_turnos', HorarioTurnoController::class)
        ->except(['show']);

});



Route::middleware(['auth'])->group(function () {

    Route::resource('horarios', HorarioController::class)
        ->except(['show']);

});

Route::middleware(['auth'])->group(function () {

    Route::resource('asignacion_horarios', AsignacionHorarioController::class)
        ->except(['show']);

});

Route::middleware(['auth'])->group(function () {

    Route::resource('excepcion_empleados', ExcepcionEmpleadoController::class)
        ->except(['show']);

});

Route::middleware(['auth'])->group(function () {
    Route::get('marcaciones/importar', [MarcacionCrudaController::class, 'create'])
        ->name('marcaciones.create');

    Route::post('marcaciones/importar', [MarcacionCrudaController::class, 'store'])
        ->name('marcaciones.store');
});


Route::middleware(['auth'])->group(function () {

    Route::get('asistencias', 
        [ProcesarAsistenciaController::class, 'index']
    )->name('asistencias.index');

    Route::post('asistencias/procesar', 
        [ProcesarAsistenciaController::class, 'procesar']
    )->name('asistencias.procesar');
    Route::post('/asistencias/reprocesar',
     [ProcesarAsistenciaController::class, 'reprocesar']
     )->name('asistencias.reprocesar');

});
Route::get('asistencias/pdf', [ProcesarAsistenciaController::class, 'exportarPdf'])->name('asistencias.pdf');

Route::put('/asistencias/{asistencia}/manual', [ProcesarAsistenciaController::class, 'actualizacionManual'])->name('asistencias.manual');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
