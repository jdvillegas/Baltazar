<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaseModel;

class CaseSeeder extends Seeder
{
    public function run()
    {
        // Obtener el usuario administrador
        $admin = \App\Models\User::where('email', 'admin@example.com')->first();
        
        if ($admin) {
            CaseModel::create([
                'title' => 'Caso de Prueba',
                'description' => 'Este es un caso de prueba para verificar que todo funcione correctamente.',
                'status' => 'pendiente',
                'user_id' => $admin->id
            ]);
        }
    }
}
