<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CaseModel;
use Illuminate\Support\Facades\Log;

class CleanAnulledCases implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando limpieza de casos anulados');
            
            // Obtener la fecha límite (hace 3 días)
            $fechaLimite = now()->subDays(3);
            Log::info("Buscando casos anulados antes de: " . $fechaLimite->format('Y-m-d H:i:s'));
            
            // Consulta para ver cuántos registros coinciden
            $casosAEliminar = CaseModel::where('status', 'anulado')
                ->where('anulled_at', '<=', $fechaLimite)
                ->get();
                
            Log::info("Se encontraron " . $casosAEliminar->count() . " casos para eliminar");
            
            // Mostrar información de los casos que se van a eliminar
            foreach ($casosAEliminar as $caso) {
                Log::info(sprintf(
                    "Eliminando caso ID: %d, Anulado el: %s, Estado: %s",
                    $caso->id,
                    $caso->anulled_at,
                    $caso->status
                ));
            }
            
            // Eliminar los casos
            $eliminados = CaseModel::where('status', 'anulado')
                ->where('anulled_at', '<=', $fechaLimite)
                ->delete();

            Log::info("Se eliminaron $eliminados casos anulados exitosamente");
            
        } catch (\Exception $e) {
            Log::error('Error al limpiar casos anulados: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
