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
            // Eliminar casos anulados que tienen más de 15 días
            CaseModel::where('status', 'anulado')
                ->where('anulled_at', '<=', now()->subDays(15))
                ->delete();

            Log::info('Casos anulados limpiados exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al limpiar casos anulados: ' . $e->getMessage());
            throw $e;
        }
    }
}
