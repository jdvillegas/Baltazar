<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Create a permission if it doesn't exist
     *
     * @param string $name
     * @return Permission
     */
    protected function createPermissionIfNotExists(string $name): Permission
    {
        return Permission::firstOrCreate(['name' => $name]);
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for users
        $this->createPermissionIfNotExists('view users');
        $this->createPermissionIfNotExists('create users');
        $this->createPermissionIfNotExists('edit users');
        $this->createPermissionIfNotExists('delete users');

        // Create permissions for cases
        $this->createPermissionIfNotExists('view cases');
        $this->createPermissionIfNotExists('create cases');
        $this->createPermissionIfNotExists('edit cases');
        $this->createPermissionIfNotExists('delete cases');
        $this->createPermissionIfNotExists('assign cases');
        $this->createPermissionIfNotExists('resolve cases');

        // Create permissions for support tickets
        $this->createPermissionIfNotExists('view support_tickets');
        $this->createPermissionIfNotExists('create support_tickets');
        $this->createPermissionIfNotExists('edit support_tickets');
        $this->createPermissionIfNotExists('delete support_tickets');
        $this->createPermissionIfNotExists('assign support_tickets');
        $this->createPermissionIfNotExists('resolve support_tickets');

        // Create roles and assign created permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->syncPermissions([
            'view cases', 'create cases', 'edit cases', 'assign cases', 'resolve cases',
            'view support_tickets', 'create support_tickets', 'edit support_tickets', 
            'assign support_tickets', 'resolve support_tickets'
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'view cases', 'create cases',
            'view support_tickets', 'create support_tickets'
        ]);

        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to the admin user
        $admin->assignRole('admin');
    }
}
