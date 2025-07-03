<?php

namespace App\Console\Commands;

use App\Services\ActuacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SincronizarTodasActuaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actuaciones:sincronizar-todas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las actuaciones para todos los casos activos';

    /**
     * The ActuacionService instance.
     *
     * @var ActuacionService
     */
    protected $actuacionService;

    /**
     * Create a new command instance.
     *
     * @param ActuacionService $actuacionService
     * @return void
     */
    public function __construct(ActuacionService $actuacionService)
    {
        parent::__construct();
        $this->actuacionService = $actuacionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de actuaciones para todos los casos activos...');
        
        $stats = $this->actuacionService->sincronizarActuacionesParaCasosActivos();
        
        // Mostrar resumen
        $this->info("\nResumen de la sincronización:");
        $this->line("Total de casos procesados: {$stats['total_casos']}");
        $this->line("Casos exitosos: {$stats['casos_exitosos']}");
        $this->line("Casos fallidos: {$stats['casos_fallidos']}");
        $this->line("Tiempo de ejecución: {$stats['tiempo_ejecucion']} segundos");
        
        // Mostrar errores si los hay
        if (!empty($stats['errores'])) {
            $this->warn("\nErrores encontrados:");
            foreach ($stats['errores'] as $casoId => $error) {
                $this->error("Caso ID {$casoId} (Radicado: {$error['radicado']}): {$error['error']}");
            }
        }
        
        $this->info("\n¡Sincronización completada!");
        
        return 0;
    }
}
