<?php

namespace Tests\Unit;

use App\Models\CaseModel;
use App\Services\ActuacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActuacionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sincronizar_actuaciones()
    {
        // Obtener el primer caso de prueba
        $case = CaseModel::first();
        
        if (!$case) {
            $this->markTestSkipped('No hay casos en la base de datos para probar');
            return;
        }

        // Datos de ejemplo de actuaciones
        $actuacionesEjemplo = [
            [
                'idRegActuacion' => '12345',
                'llaveProceso' => $case->llave_proceso,
                'fechaRegistro' => '2025-07-01',
                'fechaInicial' => '2025-07-01',
                'fechaFinal' => '2025-07-01',
                'fechaActuacion' => '2025-07-01',
                'consActuacion' => 1,
                'conDocumento' => true,
                'codRegla' => 'COD123',
                'cant' => 1,
                'anotacion' => 'Esta es una anotación de prueba',
                'actuacion' => 'Actuación de prueba'
            ]
        ];

        // Instanciar el servicio
        $service = new ActuacionService();
        
        // Ejecutar la función a probar
        $service->sincronizarActuaciones($case, $actuacionesEjemplo);

        // Verificar que se creó la actuación
        $this->assertDatabaseHas('actuaciones', [
            'case_model_id' => $case->id,
            'idRegActuacion' => '12345',
            'actuacion' => 'Actuación de prueba'
        ]);

        // Verificar que la relación funciona
        $this->assertCount(1, $case->actuaciones);
    }
}
