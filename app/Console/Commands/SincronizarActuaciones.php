<?php

namespace App\Console\Commands;

use App\Models\CaseModel;
use App\Services\Contracts\ActuacionServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SincronizarActuaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actuaciones:sync {case_id? : ID del caso a sincronizar} {--all : Sincronizar todos los casos activos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las actuaciones de un caso o de todos los casos activos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ActuacionServiceInterface $actuacionService)
    {
        if ($this->option('all')) {
            return $this->sincronizarTodos($actuacionService);
        }

        $caseId = $this->argument('case_id');
        
        if (!$caseId) {
            $this->error('Debe especificar un ID de caso o usar la opción --all');
            return 1;
        }

        $case = CaseModel::find($caseId);
        
        if (!$case) {
            $this->error("No se encontró el caso con ID: {$caseId}");
            return 1;
        }

        return $this->sincronizarCaso($case, $actuacionService);
    }

    /**
     * Sincroniza un caso específico
     *
     * @param CaseModel $case
     * @param ActuacionServiceInterface $actuacionService
     * @return int
     */
    protected function sincronizarCaso(CaseModel $case, ActuacionServiceInterface $actuacionService): int
    {
        $this->info("Sincronizando actuaciones para el caso ID: {$case->id} - {$case->title}");
        
        try {
            $actuaciones = $actuacionService->consultarActuaciones($case);
            $actuacionService->sincronizarActuaciones($case, $actuaciones);
            
            $this->info("✓ Sincronización completada para el caso ID: {$case->id}");
            $this->info("  - Total de actuaciones: " . count($actuaciones));
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error al sincronizar el caso ID: {$case->id}");
            $this->error("  - Error: " . $e->getMessage());
            
            Log::error('Error al sincronizar actuaciones', [
                'case_id' => $case->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }

    /**
     * Sincroniza todos los casos activos
     *
     * @param ActuacionServiceInterface $actuacionService
     * @return int
     */
    protected function sincronizarTodos(ActuacionServiceInterface $actuacionService): int
    {
        $cases = CaseModel::whereIn('status', ['pendiente', 'en_proceso'])
            ->whereNotNull('id_proceso')
            ->get();

        if ($cases->isEmpty()) {
            $this->info('No hay casos activos para sincronizar');
            return 0;
        }

        $this->info("Iniciando sincronización para {$cases->count()} casos activos...");
        
        $success = 0;
        $errors = 0;
        
        $bar = $this->output->createProgressBar($cases->count());
        $bar->start();
        
        foreach ($cases as $case) {
            $result = $this->sincronizarCaso($case, $actuacionService);
            
            if ($result === 0) {
                $success++;
            } else {
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Resumen de sincronización:");
        $this->info("  - Total de casos procesados: " . $cases->count());
        $this->info("  - Sincronizaciones exitosas: {$success}");
        $this->info("  - Errores: {$errors}");
        
        return $errors > 0 ? 1 : 0;
    }
}
