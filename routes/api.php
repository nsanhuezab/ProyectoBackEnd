<?php

use App\Http\Controllers\Api\V1\ApplicationApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PracticeApiController;
use App\Http\Controllers\Api\V1\PublicDirectoryController;
use App\Http\Controllers\Api\V1\PublicOfferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST v1  (prefijo automático /api)
|--------------------------------------------------------------------------
| Públicas (sin autenticación):
|   API Pública 1 - Ofertas de práctica      /api/v1/public/offers
|   API Pública 2 - Carreras y empresas      /api/v1/public/careers | companies
| Privadas (Authorization: Bearer <token>):
|   Autenticación                             /api/v1/auth/*
|   API Privada 1 - Postulaciones             /api/v1/applications
|   API Privada 2 - Prácticas y bitácoras     /api/v1/practices
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ---------------------------------------------------------------- Públicas
    Route::prefix('public')->name('public.')->middleware('throttle:60,1')->group(function () {
        Route::get('offers', [PublicOfferController::class, 'index'])->name('offers.index');
        Route::get('offers/{id}', [PublicOfferController::class, 'show'])->whereNumber('id')->name('offers.show');

        Route::get('careers', [PublicDirectoryController::class, 'careers'])->name('careers.index');
        Route::get('companies', [PublicDirectoryController::class, 'companies'])->name('companies.index');
        Route::get('companies/{id}', [PublicDirectoryController::class, 'company'])->whereNumber('id')->name('companies.show');
    });

    // ---------------------------------------------------------------- Privadas
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('auth.login');

    Route::middleware(['api.token', 'throttle:120,1'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('applications', [ApplicationApiController::class, 'index'])->name('applications.index');
        Route::post('applications', [ApplicationApiController::class, 'store'])->name('applications.store');
        Route::get('applications/{id}', [ApplicationApiController::class, 'show'])->whereNumber('id')->name('applications.show');
        Route::delete('applications/{id}', [ApplicationApiController::class, 'destroy'])->whereNumber('id')->name('applications.destroy');

        Route::get('practices', [PracticeApiController::class, 'index'])->name('practices.index');
        Route::get('practices/{id}', [PracticeApiController::class, 'show'])->whereNumber('id')->name('practices.show');
        Route::get('practices/{id}/logs', [PracticeApiController::class, 'logs'])->whereNumber('id')->name('practices.logs.index');
        Route::post('practices/{id}/logs', [PracticeApiController::class, 'storeLog'])->whereNumber('id')->name('practices.logs.store');
    });
});
