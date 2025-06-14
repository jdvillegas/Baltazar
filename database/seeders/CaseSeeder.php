<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaseModel;

class CaseSeeder extends Seeder
{
    public function run()
    {
        CaseModel::create([
            'title' => 'Caso de Prueba',
            'description' => 'Este es un caso de prueba para verificar que todo funcione correctamente.',
            'status' => 'pendiente',
            'user_id' => 1, // Asumiendo que existe un usuario con ID 1
        ]);
    }
}
