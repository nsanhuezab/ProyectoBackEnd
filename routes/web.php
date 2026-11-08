<?php

use App\Http\Controllers\Admin\ManagementPageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\UseLocalDemoUser;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

$dashboardMiddleware = app()->environment('local') && config('app.demo_mode')
    ? [UseLocalDemoUser::class]
    : ['auth', 'verified'];
$administratorMiddleware = app()->environment('local') && config('app.demo_mode')
    ? [UseLocalDemoUser::class]
    : ['auth', 'verified', 'role:Administrador|Super Administrador'];

Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware($dashboardMiddleware)
    ->name('dashboard');

Route::prefix('admin/users')
    ->name('admin.users.')
    ->middleware($administratorMiddleware)
    ->controller(UserController::class)
    ->group(function () {
        Route::post('/', 'store')->name('store');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

Route::get('/demo', function () {
    abort_unless(app()->environment('local') && config('app.demo_mode'), 404);

    return redirect()->route('dashboard');
})->name('demo');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

$staffMiddleware = app()->environment('local') && config('app.demo_mode')
    ? [UseLocalDemoUser::class]
    : ['auth', 'verified', 'role:Administrador|Super Administrador|Coordinador'];
$offersMiddleware = app()->environment('local') && config('app.demo_mode')
    ? [UseLocalDemoUser::class]
    : ['auth', 'role:Administrador|Super Administrador|Coordinador|Empresa'];

/*
 | Mantenedores (sin prefijo /admin). Cada módulo tiene su controlador y su vista.
 */

// Solo Administrador y Super Administrador
Route::middleware($administratorMiddleware)->group(function () {
    Route::get('careers/options', [CareerController::class, 'options'])->name('careers.options');
    Route::resource('careers', CareerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('coordinators', CoordinatorController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['coordinators' => 'coordinator']);
    Route::resource('students', StudentController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('companies', CompanyController::class)->only(['index', 'store', 'update', 'destroy']);
});

// Administración y Coordinador (el coordinador solo ve lo de su carrera)
Route::middleware($staffMiddleware)->group(function () {
    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('applications/{application}/assign', [ApplicationController::class, 'assign'])->name('applications.assign');
    Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::resource('practices', PracticeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('practices/{practice}/transition', [PracticeController::class, 'transition'])->name('practices.transition');

    Route::post('offers/{offer}/approve', [OfferController::class, 'approve'])->name('offers.approve');
});

// Ofertas: Administración, Coordinador (aprueba) y Empresa (publica las suyas)
Route::middleware($offersMiddleware)->group(function () {
    Route::resource('offers', OfferController::class)->only(['index', 'store', 'update', 'destroy']);
});

// Área estudiante
Route::middleware(['auth', 'role:Estudiante'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('offers', [ManagementPageController::class, 'studentOffers'])->name('offers.view');
        Route::get('offers/data', [OfferController::class, 'studentData'])->name('offers.data');
        Route::get('my-applications', [ManagementPageController::class, 'studentApplications'])->name('applications.view');
        Route::get('applications', [ApplicationController::class, 'studentData'])->name('applications.index');
        Route::post('applications', [ApplicationController::class, 'store'])->name('applications.store');
        Route::delete('applications/{application}', [ApplicationController::class, 'studentDestroy'])->name('applications.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('applications/{application}/cv', [ApplicationController::class, 'cv'])->name('applications.cv');
});

require __DIR__.'/auth.php';
