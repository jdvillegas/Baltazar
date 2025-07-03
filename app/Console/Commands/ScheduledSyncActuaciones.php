<?php

namespace App\Console\Commands;

use App\Models\CaseModel;
use App\Services\Contracts\ActuacionServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduledSyncActuaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actuaciones:schedule-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las actuaciones de todos los casos activos de forma programada';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ActuacionServiceInterface $actuacionService)
    {
        $cases = CaseModel::whereIn('status', ['pendiente', 'en_proceso'])
            ->whereNotNull('id_proceso')
            ->get();

        if ($cases->isEmpty()) {
            $this->info('No hay casos activos para sincronizar');
            return 0;
        }

        $this->info("Iniciando sincronización programada para {$cases->count()} casos activos...");
        
        $success = 0;
        $errors = 0;
        
        $bar = $this->output->createProgressBar($cases->count());
        $bar->start();
        
        foreach ($cases as $case) {
            try {
                $actuaciones = $actuacionService->consultarActuaciones($case);
                $actuacionService->sincronizarActuaciones($case, $actuaciones);
                $success++;
            } catch (\Exception $e) {
                Log::error('Error en sincronización programada de actuaciones', [
                    'case_id' => $case->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errors++;
            }
            
            $bar->advance();
            
            // Pequeña pausa para no saturar el servidor
            sleep(1);
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Resumen de sincronización programada:");
        $this->info("  - Total de casos procesados: " . $cases->count());
        $this->info("  - Sincronizaciones exitosas: {$success}");
        $this->info("  - Errores: {$errors}");
        
        // Registrar en el log del sistema
        Log::info('Sincronización programada de actuaciones completada', [
            'total_casos' => $cases->count(),
            'exitosas' => $success,
            'errores' => $errors
        ]);
        
        return 0;
    }
}
