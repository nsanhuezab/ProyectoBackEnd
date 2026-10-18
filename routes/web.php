<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ManagementPageController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\PracticeLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\UseLocalDemoUser;
use App\Models\Career;
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

Route::middleware($administratorMiddleware)
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('management/{resource}', [ManagementPageController::class, 'index'])->name('management.index');
        Route::get('careers/view', fn () => redirect()->route('admin.management.index', 'careers'))->name('careers.view');

        Route::get('careers/options', [CareerController::class, 'options'])->name('careers.options');
        Route::apiResource('careers', CareerController::class)->except('show');
        Route::apiResource('offers', OfferController::class)->except('show');
        Route::apiResource('coordinators', CoordinatorController::class)->except('show');
        Route::apiResource('companies', CompanyController::class)->except('show');
        Route::apiResource('students', StudentController::class)->except('show');
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('practices', PracticeController::class)->except('show');
        Route::post('offers/{offer}/approve', [OfferController::class, 'approve'])->name('offers.approve');
        Route::post('applications/{application}/assign', [ApplicationController::class, 'assign'])->name('applications.assign');
        Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
        Route::post('practices/{practice}/transition', [PracticeController::class, 'transition'])->name('practices.transition');
        Route::apiResource('practices.logs', PracticeLogController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('practices.evaluations', EvaluationController::class)->only(['index', 'update', 'destroy']);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export.xlsx', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('reports/export.csv', [ReportController::class, 'exportCsv'])->name('reports.export');
    });

Route::middleware(['auth', 'verified', 'role:Coordinador'])
    ->prefix('coordinator')
    ->name('coordinator.')
    ->group(function () {
        Route::get('management/{resource}', [ManagementPageController::class, 'index'])->name('management.index');
        Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
        Route::post('offers/{offer}/approve', [OfferController::class, 'approve'])->name('offers.approve');
        Route::apiResource('applications', ApplicationController::class)->only(['index', 'update', 'destroy']);
        Route::post('applications/{application}/assign', [ApplicationController::class, 'assign'])->name('applications.assign');
        Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
        Route::apiResource('practices', PracticeController::class)->except('show');
        Route::post('practices/{practice}/transition', [PracticeController::class, 'transition'])->name('practices.transition');
        Route::apiResource('practices.logs', PracticeLogController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('practices.evaluations', EvaluationController::class)->only(['index', 'update', 'destroy']);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export.xlsx', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('reports/export.csv', [ReportController::class, 'exportCsv'])->name('reports.export');
    });

Route::middleware(['auth', 'role:Empresa|Administrador|Super Administrador'])
    ->prefix('company')
    ->name('company.')
    ->group(function () {
        Route::get('offers', [ManagementPageController::class, 'companyOffers'])->name('offers.view');
        Route::get('offers/data', [OfferController::class, 'index'])->name('offers.data');
        Route::get('practices', [ManagementPageController::class, 'companyPractices'])->name('practices.view');
        Route::get('practices/data', [PracticeController::class, 'index'])->name('practices.data');
        Route::post('offers', [OfferController::class, 'store'])->name('offers.store');
        Route::put('offers/{offer}', [OfferController::class, 'update'])->name('offers.update');
        Route::delete('offers/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');
    });

Route::middleware(['auth', 'role:Estudiante'])
    ->group(function () {
        Route::get('offers', [ManagementPageController::class, 'studentOffers'])->name('student.offers.view');
        Route::get('offers/data', [OfferController::class, 'index'])->name('student.offers.data');
        Route::get('tracking', [ManagementPageController::class, 'studentTracking'])->name('student.tracking.view');
        Route::get('practices', [PracticeController::class, 'index'])->name('student.practices.index');
        Route::get('practices/{practice}/logs', [PracticeLogController::class, 'index'])->name('student.practices.logs.index');
        Route::get('student/applications', [ApplicationController::class, 'index'])->name('student.applications.index');
        Route::get('student/my-applications', [ManagementPageController::class, 'studentApplications'])->name('student.applications.view');
        Route::post('applications', [ApplicationController::class, 'store'])->name('applications.store');
        Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::post('practices/{practice}/logs', [PracticeLogController::class, 'store'])->name('practices.logs.store');
    });

Route::middleware('auth')->group(function () {
    Route::get('applications/{application}/cv', [ApplicationController::class, 'cv'])->name('applications.cv');
    Route::post('practices/{practice}/evaluations', [EvaluationController::class, 'store'])
        ->middleware('role:Empresa|Coordinador|Administrador|Super Administrador|Estudiante')
        ->name('practices.evaluations.store');
    Route::get('practices/{practice}/acta', [ReportController::class, 'acta'])->name('practices.acta');
});

require __DIR__ . '/auth.php';
