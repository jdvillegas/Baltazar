<?php

use App\Models\CaseModel;
use App\Services\ActuacionService;
use Illuminate\Support\Facades\DB;

// Cargar el autoloader de Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Obtener el primer caso
$case = CaseModel::first();

if (!$case) {
    die("No hay casos en la base de datos para probar\n");
}

echo "Caso encontrado - ID: " . $case->id . "\n";
echo "Llave de proceso: " . $case->llave_proceso . "\n\n";

// Crear un ID de prueba único
$testId = 'TEST_' . time();

// Datos de ejemplo de actuaciones
$actuacionesEjemplo = [
    [
        'idRegActuacion' => $testId,
        'llaveProceso' => $case->llave_proceso,
        'fechaRegistro' => '2025-07-01',
        'fechaInicial' => '2025-07-01',
        'fechaFinal' => '2025-07-01',
        'fechaActuacion' => '2025-07-01',
        'consActuacion' => 1,
        'conDocumento' => true,
        'codRegla' => 'TEST_RULE',
        'cant' => 1,
        'anotacion' => 'Prueba de sincronización de actuación',
        'actuacion' => 'Actuación de prueba'
    ]
];

// Mostrar los datos que se intentarán insertar
echo "=== Datos de prueba ===\n";
foreach ($actuacionesEjemplo[0] as $key => $value) {
    echo "$key: " . (is_string($value) ? "\"$value\"" : json_encode($value)) . " (" . gettype($value) . ")\n";
}
echo "\n";

// Instanciar el servicio
$service = new ActuacionService();

try {
    echo "Iniciando sincronización...\n";
    
    // Ejecutar la sincronización
    $service->sincronizarActuaciones($case, $actuacionesEjemplo);
    
    echo "¡Sincronización completada!\n";
    
    // Verificar que la actuación se haya guardado
    $actuacion = $case->actuaciones()
        ->where('idRegActuacion', $testId)
        ->first();
    
    if ($actuacion) {
        echo "\n=== Actuación guardada correctamente ===\n";
        echo "ID: " . $actuacion->id . "\n";
        echo "ID de registro: " . $actuacion->idRegActuacion . "\n";
        echo "Actuación: " . $actuacion->actuacion . "\n";
        echo "Creado: " . $actuacion->created_at . "\n";
    } else {
        echo "\nError: No se pudo encontrar la actuación recién creada.\n";
        
        // Mostrar las actuaciones existentes para depuración
        $actuaciones = $case->actuaciones()->get();
        echo "\nActuaciones existentes (" . $actuaciones->count() . "):\n";
        foreach ($actuaciones as $a) {
            echo "- " . $a->idRegActuacion . ": " . $a->actuacion . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "\n=== Error al sincronizar actuaciones ===\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    
    if ($e->getPrevious()) {
        echo "Causa: " . $e->getPrevious()->getMessage() . "\n";
    }
    
    // Mostrar el trace completo para depuración
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
