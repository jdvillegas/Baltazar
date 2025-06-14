<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear el usuario administrador
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'membership_type' => 'premium',
            'max_open_cases' => 100,
            'status' => 'active'
        ]);

        // Verificar que el rol existe
        $role = \App\Models\Role::where('name', 'admin')->first();
        if ($role) {
            // Asignar el rol de administrador
            $admin->assignRole($role);
        } else {
            // Si el rol no existe, crearlo y asignarlo
            $role = \App\Models\Role::create(['name' => 'admin']);
            $admin->assignRole($role);
        }
    }
}
