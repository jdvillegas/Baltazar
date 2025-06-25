<?php

namespace App\Http\Controllers;

use App\Jobs\CleanAnulledCases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestJobController extends Controller
{
    public function runJob()
    {
        try {
            Log::info('Iniciando ejecución manual de CleanAnulledCases');
            $job = new CleanAnulledCases();
            $job->handle();
            Log::info('Ejecución de CleanAnulledCases completada');
            return response()->json(['message' => 'Job ejecutado exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error al ejecutar el job: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
