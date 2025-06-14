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

    // Rutas de administración
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UsersController::class);
        Route::delete('users/{user}/inactivate', [\App\Http\Controllers\Admin\UsersController::class, 'inactivate'])->name('admin.users.inactivate');
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationsController::class);
        Route::post('notifications/{notification}/send', [\App\Http\Controllers\Admin\NotificationsController::class, 'send'])->name('admin.notifications.send');
        Route::resource('support', \App\Http\Controllers\Admin\SupportController::class);
        Route::post('support/{ticket}/resolve', [\App\Http\Controllers\Admin\SupportController::class, 'resolve'])->name('admin.support.resolve');
    });

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
