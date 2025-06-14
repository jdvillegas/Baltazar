<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inicializar el registro de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $clientRole = Role::create(['name' => 'client', 'guard_name' => 'web']);

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
            $perm = \App\Models\Permission::create(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        // Asignar permisos básicos a clientes
        $clientRole->givePermissionTo(\App\Models\Permission::whereIn('name', [
            'cases.view',
            'cases.create',
            'cases.edit',
            'cases.delete'
        ])->where('guard_name', 'web')->get());

        // Crear superadmin
        $user = \App\Models\User::firstOrCreate([
            'email' => 'admin@baltazar.com'
        ], [
            'name' => 'Super Administrador',
            'password' => \Hash::make('password'),
            'membership_type' => 'premium',
            'max_open_cases' => 100
        ]);

        $user->assignRole(\App\Models\Role::where('name', 'admin')->first());
    }
}
