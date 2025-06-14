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
        if (!$role) {
            // Si el rol no existe, crearlo
            $role = \App\Models\Role::create(['name' => 'admin']);
        }

        // Asignar el rol de administrador
        $admin->assignRole($role);

        // Verificar si el rol se asignó correctamente
        if ($admin->hasRole('admin')) {
            echo "Usuario administrador creado y asignado correctamente\n";
        } else {
            echo "Error: No se pudo asignar el rol de administrador\n";
        }
    }
}
