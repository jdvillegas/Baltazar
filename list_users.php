<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $users = DB::table('users')
        ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->select('users.id', 'users.name', 'users.email', 'users.status', 'roles.name as role')
        ->get();
    
    if ($users->isEmpty()) {
        echo "No hay usuarios registrados en la plataforma.\n";
    } else {
        echo "Usuarios registrados en la plataforma (" . $users->count() . "):\n\n";
        echo str_pad("ID", 5) . " | " . 
             str_pad("Nombre", 25) . " | " . 
             str_pad("Email", 30) . " | " . 
             str_pad("Estado", 10) . " | " . 
             "Rol\n";
        echo str_repeat("-", 85) . "\n";
        
        foreach ($users as $user) {
            echo str_pad($user->id, 5) . " | " . 
                 str_pad(substr($user->name, 0, 23), 25) . " | " . 
                 str_pad(substr($user->email, 0, 28), 30) . " | " . 
                 str_pad($user->status ?? 'N/A', 10) . " | " . 
                 ($user->role ?? 'Sin rol') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error al consultar los usuarios: " . $e->getMessage() . "\n";
}
