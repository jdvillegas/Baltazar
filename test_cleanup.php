<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\CaseModel;
use Carbon\Carbon;

// Configurar la aplicación
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configurar el logger para que escriba en un archivo específico
$logFile = storage_path('logs/cleanup_debug.log');

// Usar el logger de Laravel
$logger = Log::getLogger();
$logger->pushHandler(new \Monolog\Handler\StreamHandler($logFile, \Monolog\Level::Debug));

// Función para escribir en el log
try {
    $logger->info('=== Iniciando prueba de limpieza de casos anulados ===');
    
    // 1. Verificar conexión a la base de datos
    $logger->info('Verificando conexión a la base de datos...');
    
    // 2. Contar casos anulados
    $totalAnulados = CaseModel::where('status', 'anulado')->count();
    $logger->info("Total de casos anulados en la base de datos: $totalAnulados");
    
    // 3. Mostrar información de los casos anulados
    $casos = CaseModel::where('status', 'anulado')
        ->orderBy('anulled_at', 'desc')
        ->get(['id', 'status', 'anulled_at', 'created_at', 'updated_at']);
    
    $logger->info("Detalles de los casos anulados:");
    foreach ($casos as $caso) {
        $logger->info(sprintf(
            "- ID: %d, Status: %s, Anulado: %s, Creado: %s, Actualizado: %s",
            $caso->id,
            $caso->status,
            $caso->anulled_at,
            $caso->created_at,
            $caso->updated_at
        ));
    }
    
    // 4. Intentar eliminar casos anulados con más de 3 días
    $fechaLimite = Carbon::now()->subDays(3);
    $logger->info("Intentando eliminar casos anulados antes de: " . $fechaLimite->format('Y-m-d H:i:s'));
    
    $query = CaseModel::where('status', 'anulado')
        ->where('anulled_at', '<=', $fechaLimite);
    
    // Mostrar la consulta SQL que se ejecutará
    $sql = $query->toSql();
    $bindings = $query->getBindings();
    
    $logger->info("Consulta SQL: $sql");
    $logger->info("Bindings: " . json_encode($bindings));
    
    // Mostrar cuántos registros coinciden con la consulta
    $coincidencias = $query->count();
    $logger->info("Registros que coinciden con la consulta: $coincidencias");
    
    // Solo intentar eliminar si hay registros que coincidan
    if ($coincidencias > 0) {
        $eliminados = $query->delete();
        $logger->info("Casos eliminados: $eliminados");
    } else {
        $logger->info("No hay registros para eliminar");
    }
    
    // 5. Verificar si quedan casos después de la eliminación
    $restantes = CaseModel::where('status', 'anulado')->count();
    $logger->info("Casos anulados restantes: $restantes");
    
    $logger->info('=== Prueba completada ===');
    
    echo "Prueba completada. Verifica el archivo de log: $logFile\n";
    
} catch (\Exception $e) {
    $logger->error('Error en la prueba de limpieza: ' . $e->getMessage());
    $logger->error($e->getTraceAsString());
    
    echo "Ocurrió un error. Verifica el archivo de log: $logFile\n";
    exit(1);
}
