<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TestJobController;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Ruta temporal para probar el job de limpieza de casos anulados
Route::get('/test-job', [TestJobController::class, 'runJob']);

// Ruta de prueba temporal
Route::get('/test-route', function() {
    return '¡La ruta de prueba funciona correctamente!';
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', ['App\Http\Controllers\DashboardController', 'index'])->name('dashboard');
    Route::get('/dashboard', ['App\Http\Controllers\DashboardController', 'index'])->name('dashboard.redirect');

    // Rutas de administración (requieren rol de admin)
    Route::prefix('admin')->middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UsersController::class);
        Route::delete('users/{user}/inactivate', [\App\Http\Controllers\Admin\UsersController::class, 'inactivate'])->name('users.inactivate');
        Route::post('users/{user}/activate', [\App\Http\Controllers\Admin\UsersController::class, 'activate'])->name('users.activate');
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationsController::class);
        Route::post('notifications/{notification}/send', [\App\Http\Controllers\Admin\NotificationsController::class, 'send'])->name('admin.notifications.send');
        
        // Rutas de soporte
        Route::resource('support', \App\Http\Controllers\Admin\SupportController::class)->names([
            'index' => 'admin.support.index',
            'create' => 'admin.support.create',
            'store' => 'admin.support.store',
            'show' => 'admin.support.show',
            'edit' => 'admin.support.edit',
            'update' => 'admin.support.update',
            'destroy' => 'admin.support.destroy',
        ]);
        Route::post('support/{ticket}/resolve', [\App\Http\Controllers\Admin\SupportController::class, 'resolve'])->name('admin.support.resolve');
    });
    

    // Casos
    Route::prefix('cases')->group(function () {
        Route::get('/', ['App\Http\Controllers\CasesController', 'index'])->name('cases.index');
        Route::post('/{case}/actualizar-actuaciones', ['App\Http\Controllers\CasesController', 'actualizarActuaciones'])->name('cases.actualizar-actuaciones');
        Route::post('/{case}/sync-actuaciones', ['App\Http\Controllers\CasesController', 'syncActuaciones'])
            ->name('cases.sync-actuaciones');
        Route::get('/create', ['App\Http\Controllers\CasesController', 'create'])->name('cases.create');
        Route::post('/', ['App\Http\Controllers\CasesController', 'store'])->name('cases.store');
        
        // Rutas para búsqueda de procesos (deben ir antes de las rutas con parámetros)
        Route::get('/buscar', ['App\Http\Controllers\CasesController', 'buscar'])->name('cases.buscar');
        Route::post('/buscar/proceso', ['App\Http\Controllers\CasesController', 'buscarProceso'])->name('cases.buscar.proceso');
        Route::post('/guardar-proceso', ['App\Http\Controllers\CasesController', 'guardarProceso'])->name('cases.guardar_proceso');
        
        // Rutas con parámetros (deben ir al final)
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
