<?php

use App\Http\Controllers\CareerController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('careers/options', [CareerController::class, 'options'])->name('careers.options');
        Route::apiResource('careers', CareerController::class)->except('show');
        Route::apiResource('offers', OfferController::class)->except('show');
        Route::apiResource('coordinators', CoordinatorController::class)->except('show');
    });

require __DIR__.'/auth.php';
