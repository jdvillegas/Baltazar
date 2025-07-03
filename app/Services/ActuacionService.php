<?php

namespace App\Services;

use App\Models\Actuacion;
use App\Models\CaseModel;
use App\Services\Contracts\ActuacionServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ActuacionService implements ActuacionServiceInterface
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://consultaprocesos.ramajudicial.gov.co:448/api/v2';
        $this->client = new Client([
            'verify' => false, // Solo para desarrollo, en producción deberías manejar los certificados SSL correctamente
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function consultarActuaciones(CaseModel $case): array
    {
        try {
            if (empty($case->llave_proceso)) {
                throw new \RuntimeException('El caso no tiene una llave de proceso válida');
            }

            // Formatear la llave de proceso según lo esperado por la API
            $llaveProceso = trim($case->llave_proceso);
            
            // Verificar que la llave de proceso tenga el formato correcto (23 dígitos)
            if (strlen($llaveProceso) !== 23) {
                throw new \RuntimeException('La llave de proceso no tiene el formato correcto (debe tener 23 dígitos)');
            }

            Log::info('Consultando actuaciones para llave_proceso: ' . $llaveProceso);
            
            try {
                // Parsear la llave_proceso para obtener sus componentes
                // Formato esperado: DEPTO_MUNICIPIO_RADICADO_AÑO_CONSECUTIVO
                $parts = explode('_', $llaveProceso);
                if (count($parts) < 4) {
                    throw new \Exception('Formato de llave_proceso inválido');
                }
                
                $departamento = $parts[0];
                $municipio = $parts[1];
                $radicado = $parts[2];
                $anio = substr($parts[3], 0, 4);
                $consecutivo = substr($parts[3], 4) ?? '001';
                
                // Primero obtener el idProceso
                $response = $this->client->get("$this->baseUrl/Procesos/Consulta/Numero", [
                    'query' => [
                        'departamento' => $departamento,
                        'municipio' => $municipio,
                        'radicado' => $radicado,
                        'anio' => $anio,
                        'consecutivo' => $consecutivo
                    ]
                ]);
                
                $proceso = json_decode($response->getBody()->getContents(), true);
                
                if (empty($proceso) || !isset($proceso[0]['idProceso'])) {
                    throw new \Exception('No se encontró el proceso con la llave proporcionada');
                }
                
                $idProceso = $proceso[0]['idProceso'];
                
                // Ahora obtener las actuaciones usando el idProceso
                $response = $this->client->get("$this->baseUrl/Proceso/Actuaciones/$idProceso");
                $actuaciones = json_decode($response->getBody()->getContents(), true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Error al decodificar la respuesta del servicio de actuaciones');
                }

                // Si la respuesta no es un array, retornar array vacío
                if (!is_array($actuaciones)) {
                    return [];
                }

                // Mapear los datos al formato esperado
                $formattedActuaciones = [];
                foreach ($actuaciones as $actuacion) {
                    $formattedActuaciones[] = [
                        'idRegActuacion' => $actuacion['idRegActuacion'] ?? null,
                        'llaveProceso' => $llaveProceso, // Usar la llave del caso
                        'fechaRegistro' => $actuacion['fechaRegistro'] ?? null,
                        'fechaInicial' => $actuacion['fechaInicial'] ?? null,
                        'fechaFinal' => $actuacion['fechaFinal'] ?? null,
                        'fechaActuacion' => $actuacion['fechaActuacion'] ?? null,
                        'consActuacion' => $actuacion['consActuacion'] ?? null,
                        'conDocumento' => $actuacion['conDocumento'] ?? false,
                        'codRegla' => $actuacion['codRegla'] ?? null,
                        'cant' => $actuacion['cant'] ?? 0,
                        'anotacion' => $actuacion['anotacion'] ?? null,
                        'actuacion' => $actuacion['actuacion'] ?? null,
                    ];
                }

                return $formattedActuaciones;
                
            } catch (\Exception $e) {
                Log::error('Error al consultar actuaciones: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error al consultar actuaciones', [
                'case_id' => $case->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \RuntimeException('No se pudieron consultar las actuaciones: ' . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function sincronizarActuaciones(CaseModel $case, array $actuaciones): void
    {
        if (empty($actuaciones)) {
            return;
        }

        DB::beginTransaction();

        try {
            // Primero obtenemos los IDs de las actuaciones existentes
            $existingIds = $case->actuaciones()->pluck('idRegActuacion')->toArray();
            $newActuaciones = [];
            $updatedCount = 0;

            foreach ($actuaciones as $actuacion) {
                // Verificar si la actuación tiene la estructura esperada
                if (!isset($actuacion['idRegActuacion'])) {
                    continue; // Saltar si no tiene el ID de registro
                }

                $actuacionData = [
                    'idRegActuacion' => $actuacion['idRegActuacion'] ?? null,
                    'llaveProceso' => $actuacion['llaveProceso'] ?? null,
                    'fechaRegistro' => $actuacion['fechaRegistro'] ?? null,
                    'fechaInicial' => $actuacion['fechaInicial'] ?? null,
                    'fechaFinal' => $actuacion['fechaFinal'] ?? null,
                    'fechaActuacion' => $actuacion['fechaActuacion'] ?? null,
                    'consActuacion' => $actuacion['consActuacion'] ?? null,
                    'conDocumento' => $actuacion['conDocumento'] ?? false,
                    'codRegla' => $actuacion['codRegla'] ?? null,
                    'cant' => $actuacion['cant'] ?? 0,
                    'anotacion' => $actuacion['anotacion'] ?? null,
                    'actuacion' => $actuacion['actuacion'] ?? null,
                ];

                // Si la actuación ya existe, la actualizamos
                if (in_array($actuacionData['idRegActuacion'], $existingIds)) {
                    $case->actuaciones()
                        ->where('idRegActuacion', $actuacionData['idRegActuacion'])
                        ->update($actuacionData);
                    $updatedCount++;
                } else {
                    // Si no existe, la agregamos al array para insertar
                    $newActuaciones[] = $actuacionData;
                }
            }

            // Insertamos las nuevas actuaciones
            if (!empty($newActuaciones)) {
                $case->actuaciones()->createMany($newActuaciones);
            }

            DB::commit();

            Log::info('Sincronización de actuaciones completada', [
                'case_id' => $case->id,
                'nuevas_actuaciones' => count($newActuaciones),
                'actuaciones_actualizadas' => $updatedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al sincronizar actuaciones', [
                'case_id' => $case->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \RuntimeException('Error al sincronizar las actuaciones: ' . $e->getMessage());
        }
    }

    /**
     * Sincroniza las actuaciones para todos los casos activos
     *
     * @return array Estadísticas de la sincronización
     */
    public function sincronizarActuacionesParaCasosActivos(): array
    {
        $startTime = microtime(true);
        $stats = [
            'total_casos' => 0,
            'casos_exitosos' => 0,
            'casos_fallidos' => 0,
            'errores' => [],
            'tiempo_ejecucion' => 0,
        ];

        // Obtener solo casos activos que tengan llave_proceso
        $casos = CaseModel::where('estado', 'activo')
            ->whereNotNull('llave_proceso')
            ->get(['id', 'llave_proceso', 'radicado']);

        $stats['total_casos'] = $casos->count();

        if ($casos->isEmpty()) {
            Log::info('No hay casos activos para sincronizar');
            return $stats;
        }

        Log::info("Iniciando sincronización de actuaciones para {$stats['total_casos']} casos activos");

        foreach ($casos as $caso) {
            try {
                Log::info("Sincronizando actuaciones para el caso ID: {$caso->id}, Radicado: {$caso->radicado}");
                
                // Consultar actuaciones desde la API
                $actuaciones = $this->consultarActuaciones($caso);
                
                // Sincronizar las actuaciones
                $this->sincronizarActuaciones($caso, $actuaciones);
                
                $stats['casos_exitosos']++;
                
                // Pequeña pausa para no saturar la API
                usleep(500000); // 0.5 segundos
                
            } catch (\Exception $e) {
                $errorMsg = "Error al sincronizar actuaciones para el caso ID: {$caso->id}, Radicado: {$caso->radicado}. Error: " . $e->getMessage();
                Log::error($errorMsg);
                
                $stats['casos_fallidos']++;
                $stats['errores'][$caso->id] = [
                    'radicado' => $caso->radicado,
                    'error' => $e->getMessage(),
                ];
                
                continue;
            }
        }

        $stats['tiempo_ejecucion'] = round(microtime(true) - $startTime, 2);
        
        Log::info("Sincronización completada", $stats);
        
        return $stats;
    }

    /**
     * @inheritDoc
     */
    public function actualizarActuacionesDelCaso(CaseModel $case): array
    {
        $startTime = microtime(true);
        $stats = [
            'nuevas_actuaciones' => 0,
            'actualizadas' => 0,
            'total_actuaciones' => 0,
            'exito' => false,
            'mensaje' => '',
            'tiempo_ejecucion' => 0,
        ];

        DB::beginTransaction();

        try {
            if (empty($case->llave_proceso)) {
                throw new \RuntimeException('El caso no tiene una llave de proceso válida');
            }

            Log::info("Iniciando actualización de actuaciones para el caso ID: {$case->id}, Llave: {$case->llave_proceso}");
            
            // Consultar actuaciones desde la API
            $actuacionesApi = $this->consultarActuaciones($case);
            $stats['total_actuaciones'] = count($actuacionesApi);
            
            if (empty($actuacionesApi)) {
                throw new \RuntimeException('No se encontraron actuaciones para este caso');
            }
            
            // Obtener IDs de actuaciones existentes
            $existingIds = $case->actuaciones()->pluck('idRegActuacion')->toArray();
            $newActuaciones = [];
            
            // Procesar cada actuación de la API
            foreach ($actuacionesApi as $actuacion) {
                if (!isset($actuacion['idRegActuacion'])) {
                    continue;
                }
                
                if (!in_array($actuacion['idRegActuacion'], $existingIds)) {
                    $newActuaciones[] = $actuacion;
                }
            }
            
            // Insertar nuevas actuaciones
            if (!empty($newActuaciones)) {
                $this->sincronizarActuaciones($case, $newActuaciones);
                $stats['nuevas_actuaciones'] = count($newActuaciones);
                
                // Actualizar la fecha de actualización del caso
                $case->ultima_actualizacion = now();
                $case->save();
                
                Log::info("Se agregaron {$stats['nuevas_actuaciones']} nuevas actuaciones para el caso ID: {$case->id}");
            } else {
                // Actualizar solo la fecha de verificación si no hay cambios
                $case->touch('updated_at');
                Log::info("No se encontraron nuevas actuaciones para el caso ID: {$case->id}");
            }
            
            DB::commit();
            
            $stats['exito'] = true;
            $stats['mensaje'] = $stats['nuevas_actuaciones'] > 0 
                ? "Se actualizaron {$stats['nuevas_actuaciones']} actuaciones"
                : "No hay actualizaciones disponibles";
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            $stats['exito'] = false;
            $stats['mensaje'] = 'Error al actualizar las actuaciones: ' . $e->getMessage();
            
            Log::error($stats['mensaje'], [
                'case_id' => $case->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        $stats['tiempo_ejecucion'] = round(microtime(true) - $startTime, 2);
        
        return $stats;
    }
}
