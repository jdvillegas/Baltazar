<?php

namespace App\Services;

use App\Models\CaseModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ProcesoService
{
    protected $apiUrl = 'https://consultaprocesos.ramajudicial.gov.co:448/api/v2/Procesos/Consulta/NumeroRadicacion';

    /**
     * Consulta un proceso por número de radicado
     *
     * @param string $numero Número de radicado
     * @return array
     */
    public function consultarProceso($numero)
    {
        try {
            $client = new Client([
                'verify' => false, // En producción, manejar certificados correctamente
                'timeout' => 30,
                'http_errors' => false
            ]);

            $response = $client->request('GET', $this->apiUrl, [
                'query' => [
                    'numero' => $numero,
                    'SoloActivos' => 'false',
                    'pagina' => 1
                ],
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                return [
                    'success' => true,
                    'data' => json_decode($body, true)
                ];
            }

            return [
                'success' => false,
                'error' => 'Error en la respuesta del servidor',
                'status' => $statusCode
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al consultar proceso: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al procesar la solicitud',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Guarda o actualiza un proceso en la base de datos
     *
     * @param array $datosProceso
     * @param int $userId
     * @return CaseModel
     */
    public function guardarProceso(array $datosProceso, int $userId): CaseModel
    {
        // Buscar si ya existe un caso con esta llave de proceso
        $caso = CaseModel::firstOrNew(['llave_proceso' => $datosProceso['llave_proceso']]);

        // Si es un caso existente, actualizamos los datos
        if ($caso->exists) {
            $caso->update([
                'departamento' => $datosProceso['departamento'] ?? null,
                'despacho' => $datosProceso['despacho'] ?? null,
                'fecha_proceso' => $datosProceso['fecha_proceso'] ?? null,
                'fecha_ultima_actuacion' => $datosProceso['fecha_ultima_actuacion'] ?? null,
                'id_conexion' => $datosProceso['id_conexion'] ?? null,
                'id_proceso' => $datosProceso['id_proceso'] ?? null,
                'sujetos_procesales' => $datosProceso['sujetos_procesales'] ?? null,
                'es_privado' => $datosProceso['es_privado'] ?? false,
                'status' => 'en_proceso' // Actualizamos el estado
            ]);
        } else {
            // Si es un caso nuevo, lo creamos
            $caso->fill([
                'title' => 'Proceso ' . $datosProceso['llave_proceso'],
                'description' => 'Proceso judicial consultado automáticamente',
                'departamento' => $datosProceso['departamento'] ?? null,
                'despacho' => $datosProceso['despacho'] ?? null,
                'fecha_proceso' => $datosProceso['fecha_proceso'] ?? null,
                'fecha_ultima_actuacion' => $datosProceso['fecha_ultima_actuacion'] ?? null,
                'id_conexion' => $datosProceso['id_conexion'] ?? null,
                'id_proceso' => $datosProceso['id_proceso'] ?? null,
                'llave_proceso' => $datosProceso['llave_proceso'],
                'sujetos_procesales' => $datosProceso['sujetos_procesales'] ?? null,
                'es_privado' => $datosProceso['es_privado'] ?? false,
                'status' => 'en_proceso',
                'user_id' => $userId
            ]);
            $caso->save();
        }

        return $caso;
    }

    /**
     * Procesa la respuesta de la API y guarda los datos
     *
     * @param array $respuestaApi
     * @param int $userId
     * @return array
     */
    public function procesarYGuardarRespuesta(array $respuestaApi, int $userId): array
    {
        if (empty($respuestaApi['procesos'])) {
            return [
                'success' => false,
                'message' => 'No se encontraron procesos con el número de radicación proporcionado.'
            ];
        }

        $procesosGuardados = [];
        
        foreach ($respuestaApi['procesos'] as $proceso) {
            $datosProceso = [
                'departamento' => $proceso['departamento'] ?? null,
                'despacho' => $proceso['despacho'] ?? null,
                'fecha_proceso' => $proceso['fechaProceso'] ?? null,
                'fecha_ultima_actuacion' => $proceso['fechaUltimaActuacion'] ?? null,
                'id_conexion' => $proceso['idConexion'] ?? null,
                'id_proceso' => $proceso['idProceso'] ?? null,
                'llave_proceso' => $proceso['llaveProceso'] ?? null,
                'sujetos_procesales' => $proceso['sujetosProcesales'] ?? null,
                'es_privado' => $proceso['esPrivado'] ?? false
            ];

            $caso = $this->guardarProceso($datosProceso, $userId);
            $procesosGuardados[] = $caso;
        }

        return [
            'success' => true,
            'message' => count($procesosGuardados) . ' procesos guardados/actualizados correctamente.',
            'data' => $procesosGuardados
        ];
    }
}
