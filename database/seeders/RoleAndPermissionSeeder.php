<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $clientRole = Spatie\Permission\Models\Role::create(['name' => 'client']);

        // Crear permisos
        $permissions = [
            // Gestión de usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.inactivate',
            
            // Gestión de casos
            'cases.view',
            'cases.create',
            'cases.edit',
            'cases.delete',
            
            // Gestión de notificaciones
            'notifications.view',
            'notifications.create',
            'notifications.send',
            
            // Gestión de soporte
            'support.view',
            'support.create',
            'support.resolve',
            'support.update',
            'support.delete'
        ];

        // Crear y asignar permisos
        foreach ($permissions as $permission) {
            $perm = Spatie\Permission\Models\Permission::create(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        // Asignar permisos básicos a clientes
        $clientRole->givePermissionTo([
            'cases.view',
            'cases.create',
            'cases.edit',
            'cases.delete'
        ]);

        // Crear superadmin
        $user = \App\Models\User::firstOrCreate([
            'email' => 'admin@baltazar.com'
        ], [
            'name' => 'Super Administrador',
            'password' => \Hash::make('password'),
            'membership_type' => 'premium',
            'max_open_cases' => 100
        ]);

        $user->assignRole('admin');
    }
}
