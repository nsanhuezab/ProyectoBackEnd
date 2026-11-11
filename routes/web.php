<?php

use App\Http\Controllers\Admin\ManagementPageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyPortalController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\PracticeEvaluationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPracticeController;
use App\Http\Middleware\UseLocalDemoUser;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// En modo demo (solo local) no se pide login, pero sí el rol de cada ruta.
$isDemo = app()->environment('local') && config('app.demo_mode');
$authenticated = $isDemo ? [UseLocalDemoUser::class] : ['auth', 'verified'];

$dashboardMiddleware = $authenticated;
$administratorMiddleware = [...$authenticated, 'role:Administrador|Super Administrador'];

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

$staffMiddleware = [...$authenticated, 'role:Administrador|Super Administrador|Coordinador'];
$offersMiddleware = [...$authenticated, 'role:Administrador|Super Administrador|Coordinador|Empresa'];
$companyMiddleware = [...$authenticated, 'role:Empresa|Administrador|Super Administrador'];
$anyUserMiddleware = $authenticated;

// Solo Administrador y Super Administrador
Route::middleware($administratorMiddleware)->group(function () {
    Route::get('careers/options', [CareerController::class, 'options'])->name('careers.options');
    Route::resource('careers', CareerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('coordinators', CoordinatorController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['coordinators' => 'coordinator']);
    Route::resource('students', StudentController::class)->only(['store', 'update', 'destroy']);
    Route::resource('companies', CompanyController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
});

// Administración y Coordinador (el coordinador solo ve lo de su carrera)
Route::middleware($staffMiddleware)->group(function () {
    Route::get('students', [StudentController::class, 'index'])->name('students.index');
    Route::post('students/{student}/validate', [StudentController::class, 'validateProfile'])->name('students.validate');

    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('applications/{application}/assign', [ApplicationController::class, 'assign'])->name('applications.assign');
    Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::resource('practices', PracticeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('practices/{practice}/transition', [PracticeController::class, 'transition'])->name('practices.transition');

    Route::post('offers/{offer}/approve', [OfferController::class, 'approve'])->name('offers.approve');

    // Indicadores
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('reports/excel', [ReportController::class, 'excel'])->name('reports.excel');
});

// Ofertas: Administración, Coordinador (aprueba) y Empresa (publica las suyas)
Route::middleware($offersMiddleware)->group(function () {
    Route::resource('offers', OfferController::class)->only(['index', 'store', 'update', 'destroy']);
});

// Área empresa
Route::middleware($companyMiddleware)
    ->prefix('company')
    ->name('company.')
    ->group(function () {
        Route::get('applicants', [CompanyPortalController::class, 'applicants'])->name('applicants');
        Route::get('practices', [CompanyPortalController::class, 'practices'])->name('practices');
    });

// Área estudiante
Route::middleware(['auth', 'role:Estudiante'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('offers', [ManagementPageController::class, 'studentOffers'])->name('offers.view');
        Route::get('offers/data', [OfferController::class, 'studentData'])->name('offers.data');
        Route::get('my-applications', [ManagementPageController::class, 'studentApplications'])->name('applications.view');
        Route::post('applications', [ApplicationController::class, 'store'])->name('applications.store');
        Route::delete('applications/{application}', [ApplicationController::class, 'studentDestroy'])->name('applications.destroy');

        // Mi práctica: bitácora quincenal y evaluación de la empresa
        Route::get('practice', [StudentPracticeController::class, 'index'])->name('practice.index');
        Route::post('practices/{practice}/logs', [StudentPracticeController::class, 'storeLog'])->name('practice.logs.store');
        Route::delete('practice-logs/{log}', [StudentPracticeController::class, 'destroyLog'])->name('practice.logs.destroy');
        Route::post('practices/{practice}/review', [StudentPracticeController::class, 'storeReview'])->name('practice.review.store');
    });

// Compartidas: el controlador revisa si el usuario participa en el registro
Route::middleware($anyUserMiddleware)->group(function () {
    Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('applications/{application}/messages', [ApplicationController::class, 'storeMessage'])->name('applications.messages.store');
    Route::get('applications/{application}/cv', [ApplicationController::class, 'cv'])->name('applications.cv');

    Route::get('practices/{practice}', [PracticeController::class, 'show'])->name('practices.show');
    Route::get('practices/{practice}/acta', [PracticeController::class, 'acta'])->name('practices.acta');
    Route::post('practices/{practice}/evaluations', [PracticeEvaluationController::class, 'store'])->name('practices.evaluations.store');

    Route::get('notifications/{notification}', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
});

require __DIR__.'/auth.php';
