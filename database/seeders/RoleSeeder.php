<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // create roles via spatie
        foreach (['anggota', 'BPH', 'pembina'] as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        // create dummy users
        $users = [
            ['email' => 'anggota@school.test', 'name' => 'Anggota Demo', 'role' => 'anggota', 'password' => 'password'],
            ['email' => 'bph@school.test', 'name' => 'BPH Demo', 'role' => 'BPH', 'password' => 'password'],
            ['email' => 'pembina@school.test', 'name' => 'Pembina Demo', 'role' => 'pembina', 'password' => 'password'],
        ];

        foreach ($users as $u) {
            $user = User::where('email', $u['email'])->first();
            if (! $user) {
                $user = User::create([
                    'user_id' => strtoupper(str_replace('@', '-', explode('@', $u['email'])[0])) . '-0001',
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make($u['password']),
                    'status' => 'aktif',
                    'role_name' => $u['role'],
                ]);
            } else {
                $user->update(['name' => $u['name'], 'role_name' => $u['role']]);
            }

            $user->syncRoles([$u['role']]);
        }
    }
}
