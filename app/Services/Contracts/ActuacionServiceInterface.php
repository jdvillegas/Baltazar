<?php

namespace App\Services\Contracts;

use App\Models\CaseModel;

interface ActuacionServiceInterface
{
    /**
     * Consulta las actuaciones de un proceso
     *
     * @param CaseModel $case
     * @return array
     */
    public function consultarActuaciones(CaseModel $case): array;

    /**
     * Sincroniza las actuaciones de un caso con la base de datos
     * 
     * @param CaseModel $case
     * @param array $actuaciones
     * @return void
     */
    public function sincronizarActuaciones(CaseModel $case, array $actuaciones): void;

    /**
     * Actualiza las actuaciones de un caso desde la API y las sincroniza con la base de datos
     * 
     * @param CaseModel $case
     * @return array Estadísticas de la actualización
     */
    public function actualizarActuacionesDelCaso(CaseModel $case): array;
}
