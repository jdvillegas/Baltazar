<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create or update admin user
        $admin = User::firstOrNew(['email' => 'admin@example.com']);
        
        if (!$admin->exists) {
            $admin->fill([
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'membership_type' => 'premium',
                'max_open_cases' => 100,
                'status' => 'active',
                'email_verified_at' => now()
            ]);
            $admin->save();
            
            echo "Admin user created successfully.\n";
        } else {
            echo "Admin user already exists.\n";
        }

        // Assign admin role
        $admin->syncRoles(['admin']);
        
        // Output role assignment status
        if ($admin->hasRole('admin')) {
            echo "Admin role assigned successfully.\n";
        } else {
            echo "Error: Failed to assign admin role.\n";
        }
    }
}
