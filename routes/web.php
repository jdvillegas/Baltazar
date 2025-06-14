<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', ['App\Http\Controllers\DashboardController', 'index'])->name('dashboard');

    // Casos
    Route::prefix('cases')->group(function () {
        Route::get('/', ['App\Http\Controllers\CasesController', 'index'])->name('cases.index');
        Route::get('/create', ['App\Http\Controllers\CasesController', 'create'])->name('cases.create');
        Route::post('/', ['App\Http\Controllers\CasesController', 'store'])->name('cases.store');
        Route::get('/{case}', ['App\Http\Controllers\CasesController', 'show'])->name('cases.show');
        Route::get('/{case}/edit', ['App\Http\Controllers\CasesController', 'edit'])->name('cases.edit');
        Route::put('/{case}', ['App\Http\Controllers\CasesController', 'update'])->name('cases.update');
        Route::delete('/{case}', ['App\Http\Controllers\CasesController', 'destroy'])->name('cases.destroy');
    });

    // Configuración
    Route::prefix('settings')->group(function () {
        Route::get('/', ['App\Http\Controllers\SettingsController', 'index'])->name('settings.index');
        Route::post('/', ['App\Http\Controllers\SettingsController', 'update'])->name('settings.update');
    });


});

require __DIR__.'/auth.php';
