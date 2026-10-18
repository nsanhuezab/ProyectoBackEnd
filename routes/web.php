<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\UseLocalDemoUser;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

$dashboardMiddleware = app()->environment('local') && config('app.demo_mode')
    ? [UseLocalDemoUser::class]
    : ['auth', 'verified'];

Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware($dashboardMiddleware)
    ->name('dashboard');

Route::prefix('admin/users')
    ->name('admin.users.')
    ->middleware($dashboardMiddleware)
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

require __DIR__.'/auth.php';
