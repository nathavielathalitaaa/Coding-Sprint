<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\EmployeeProfile;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure super-admin role exists
        Role::firstOrCreate(['name' => 'Pembina', 'guard_name' => 'web']);

        // Create or update the super-admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'                => 'Super Admin',
                'password'            => bcrypt('admin123'),
                'user_id'             => 'ADM-0001',
                'status'              => 'aktif',
                'role_name'           => 'Pembina',
                'must_change_password' => false,
            ]
        );

        // Assign role
        $user->syncRoles(['Pembina']);

        // Create employee profile if it doesn't exist
        EmployeeProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'jabatan'           => 'Super Administrator',
                'status_pernikahan' => 'belum_menikah',
            ]
        );

        $this->command->info('Super Admin created!');
        $this->command->info('Email    : admin@admin.com');
        $this->command->info('Password : admin123');
    }
}
