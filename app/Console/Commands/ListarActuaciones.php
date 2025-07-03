<?php

namespace App\Console\Commands;

use App\Models\Actuacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListarActuaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actuaciones:listar {--case= : ID del caso a filtrar} {--limit=10 : Número máximo de registros a mostrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista las actuaciones almacenadas en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Actuacion::query()
            ->select([
                'id',
                'idRegActuacion',
                'llaveProceso',
                'actuacion',
                'fechaActuacion',
                'created_at',
                'case_model_id'
            ])
            ->with('caseModel:id,radicado')
            ->orderBy('created_at', 'desc');

        if ($caseId = $this->option('case')) {
            $query->where('case_model_id', $caseId);
        }

        $limit = (int)$this->option('limit');
        $actuaciones = $query->limit($limit)->get();

        if ($actuaciones->isEmpty()) {
            return $this->warn('No se encontraron actuaciones' . ($this->option('case') ? ' para el caso especificado' : ''));
        }

        $this->info(sprintf(
            'Mostrando %d de %d actuaciones%s',
            $actuaciones->count(),
            $query->count(),
            $this->option('case') ? ' para el caso ID ' . $this->option('case') : ''
        ));

        $headers = ['ID', 'ID Registro', 'Radicado', 'Actuación', 'Fecha Actuación', 'Creado', 'Caso ID'];
        
        $data = $actuaciones->map(function ($actuacion) {
            return [
                $actuacion->id,
                $actuacion->idRegActuacion,
                $actuacion->caseModel ? $actuacion->caseModel->radicado : 'N/A',
                $actuacion->actuacion,
                $actuacion->fechaActuacion,
                $actuacion->created_at->format('Y-m-d H:i:s'),
                $actuacion->case_model_id
            ];
        });

        $this->table($headers, $data);

        return Command::SUCCESS;
    }
}
