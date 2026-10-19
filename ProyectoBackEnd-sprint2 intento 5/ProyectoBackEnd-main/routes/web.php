<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Coordinador;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Empresa;
use App\Http\Controllers\Estudiante;
use App\Http\Controllers\Gestion;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Inicio: al login si no hay sesión, o al dashboard si ya la hay.
Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Dashboard (general)
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Gestión y Evaluación de Prácticas  (administrador, coordinador)
    | CRUD: Prácticas, Evaluaciones, Postulaciones
    |----------------------------------------------------------------------
    */
    Route::middleware('role:coordinador')->prefix('gestion')->name('gestion.')->group(function () {
        Route::patch('postulaciones/{record}/estado', [Gestion\PostulacionController::class, 'estado'])->name('postulaciones.estado');

        Route::resource('practicas', Gestion\PracticaController::class)->except('show')->parameters(['practicas' => 'record']);
        Route::resource('evaluaciones', Gestion\EvaluacionController::class)->except('show')->parameters(['evaluaciones' => 'record']);
        Route::resource('postulaciones', Gestion\PostulacionController::class)->except('show')->parameters(['postulaciones' => 'record']);
    });

    /*
    |----------------------------------------------------------------------
    | Administración
    | CRUD: Usuarios (solo administrador) · Carreras, Empresas, Estudiantes, Ofertas
    |----------------------------------------------------------------------
    */
    Route::prefix('administracion')->name('admin.')->group(function () {
        Route::resource('usuarios', Admin\UsuarioController::class)->except('show')->parameters(['usuarios' => 'record'])
            ->middleware('role:administrador');

        Route::middleware('role:coordinador')->group(function () {
            Route::resource('carreras', Admin\CarreraController::class)->except('show')->parameters(['carreras' => 'record']);
            Route::resource('empresas', Admin\EmpresaController::class)->except('show')->parameters(['empresas' => 'record']);
            Route::resource('estudiantes', Admin\EstudianteController::class)->except('show')->parameters(['estudiantes' => 'record']);
            Route::resource('ofertas', Admin\OfertaController::class)->except('show')->parameters(['ofertas' => 'record']);
        });
    });

    /*
    |----------------------------------------------------------------------
    | COORDINADOR
    |----------------------------------------------------------------------
    */
    Route::middleware('role:coordinador')->prefix('coordinador')->name('coordinador.')->group(function () {
        Route::get('dashboard', Coordinador\DashboardController::class)->name('dashboard');
        Route::get('gestion', Coordinador\GestionController::class)->name('gestion');
    });

    /*
    |----------------------------------------------------------------------
    | ESTUDIANTE
    |----------------------------------------------------------------------
    */
    Route::middleware('role:estudiante')->prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('dashboard', Estudiante\DashboardController::class)->name('dashboard');

        Route::get('ofertas', [Estudiante\OfertaController::class, 'index'])->name('ofertas.index');
        Route::get('ofertas/{id}', [Estudiante\OfertaController::class, 'show'])->name('ofertas.show');
        Route::post('ofertas/{id}/postular', [Estudiante\OfertaController::class, 'postular'])->name('ofertas.postular');

        Route::get('postulaciones', [Estudiante\PostulacionController::class, 'index'])->name('postulaciones.index');
        Route::patch('postulaciones/{id}/retirar', [Estudiante\PostulacionController::class, 'retirar'])->name('postulaciones.retirar');

        Route::get('seguimiento', Estudiante\SeguimientoController::class)->name('seguimiento');
    });

    /*
    |----------------------------------------------------------------------
    | EMPRESA
    |----------------------------------------------------------------------
    */
    Route::middleware('role:empresa')->prefix('empresa')->name('empresa.')->group(function () {
        Route::get('dashboard', Empresa\DashboardController::class)->name('dashboard');
        Route::patch('postulaciones/{id}/estado', [Empresa\PostulacionController::class, 'estado'])->name('postulaciones.estado');

        Route::resource('ofertas', Empresa\OfertaController::class)->except('show')->parameters(['ofertas' => 'record']);
    });

    /*
    |----------------------------------------------------------------------
    | Perfil
    |----------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
