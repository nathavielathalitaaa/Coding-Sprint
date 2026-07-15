<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Surat permissions
            'surat.create',
            'surat.submit',
            'surat.view.own',
            'surat.view.pending',
            'surat.approve',

            // User & settings
            'user.manage',
            'settings.manage',

            // Misc / admin
            'system.bypass_approval',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $pembina = Role::firstOrCreate(['name' => 'pembina', 'guard_name' => 'web']);
        $bph     = Role::firstOrCreate(['name' => 'BPH', 'guard_name' => 'web']);
        $anggota = Role::firstOrCreate(['name' => 'anggota', 'guard_name' => 'web']);

        // Assign permissions
        // pembina: full access
        $pembina->givePermissionTo(Permission::all());

        // BPH: can view pending and approve/reject
        $bph->givePermissionTo(['surat.view.pending', 'surat.approve']);

        // anggota: create/submit/view own
        $anggota->givePermissionTo(['surat.create', 'surat.submit', 'surat.view.own']);

        // Create demo users
        $demoUsers = [
            ['email' => 'pembina@school.test', 'name' => 'Pembina Demo', 'role' => 'pembina', 'password' => 'password'],
            ['email' => 'bph@school.test', 'name' => 'BPH Demo', 'role' => 'BPH', 'password' => 'password'],
            ['email' => 'anggota@school.test', 'name' => 'Anggota Demo', 'role' => 'anggota', 'password' => 'password'],
        ];

        foreach ($demoUsers as $d) {
            $user = User::where('email', $d['email'])->first();
            if (! $user) {
                $user = User::create([
                    'user_id' => strtoupper(str_replace('@', '-', explode('@', $d['email'])[0])) . '-0001',
                    'name' => $d['name'],
                    'email' => $d['email'],
                    'password' => Hash::make($d['password']),
                    'status' => 'aktif',
                    'role_name' => $d['role'],
                ]);
            } else {
                $user->update(['name' => $d['name'], 'role_name' => $d['role']]);
            }

            $user->syncRoles([$d['role']]);
        }

        // Done
    }
}
