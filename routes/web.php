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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Casos
    Route::prefix('cases')->group(function () {
        Route::get('/', [CasesController::class, 'index'])->name('cases.index');
        Route::get('/create', [CasesController::class, 'create'])->name('cases.create');
        Route::post('/', [CasesController::class, 'store'])->name('cases.store');
        Route::get('/{case}', [CasesController::class, 'show'])->name('cases.show');
        Route::get('/{case}/edit', [CasesController::class, 'edit'])->name('cases.edit');
        Route::put('/{case}', [CasesController::class, 'update'])->name('cases.update');
        Route::delete('/{case}', [CasesController::class, 'destroy'])->name('cases.destroy');
    });

    // Configuración
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/', [SettingsController::class, 'update'])->name('settings.update');
    });

    // Reportes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/generate', [ReportsController::class, 'generate'])->name('reports.generate');
    });

    // Usuarios
    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::get('/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/', [UsersController::class, 'store'])->name('users.store');
        Route::get('/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
