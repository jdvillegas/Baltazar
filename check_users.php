<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $users = DB::table('users')->get();
    
    if ($users->isEmpty()) {
        echo "No hay usuarios registrados en la base de datos.\n";
    } else {
        echo "Usuarios encontrados (" . $users->count() . "):\n";
        foreach ($users as $user) {
            echo "- ID: " . $user->id . ", Email: " . ($user->email ?? 'N/A') . ", Nombre: " . ($user->name ?? 'N/A') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error al consultar la base de datos: " . $e->getMessage() . "\n";
}
