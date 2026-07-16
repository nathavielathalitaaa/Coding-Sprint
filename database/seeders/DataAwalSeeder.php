<?php

namespace Database\Seeders;

use DB;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataAwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // buat role untuk spatie permission: staff, supervisor, hr, super-admin
        foreach (['hr', 'supervisor', 'staff', 'super-admin', 'pembina', 'BPH', 'anggota'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
                [
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ambil role ids
        $hrRole = DB::table('roles')->where('name', 'hr')->first();
        $supervisorRole = DB::table('roles')->where('name', 'supervisor')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();
        $superAdminRole = DB::table('roles')->where('name', 'super-admin')->first();

        // buat user hr demo
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@company.com'],
            [
                'user_id' => 'HR-0001',
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role_name' => 'pembina', // diubah dari 'hr' ke enum yang valid
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $hrUser = DB::table('users')->where('email', 'admin@company.com')->value('id');
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $hrRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $hrUser,
        ], []);

        // buat user supervisor demo
        DB::table('users')->updateOrInsert(
            ['email' => 'supervisor@company.com'],
            [
                'user_id' => 'SUP-0001',
                'name' => 'Supervisor HR',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role_name' => 'BPH', // diubah dari 'supervisor' ke enum yang valid
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $supervisorUser = DB::table('users')->where('email', 'supervisor@company.com')->value('id');
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $supervisorRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $supervisorUser,
        ], []);

        // buat user staff demo
        DB::table('users')->updateOrInsert(
            ['email' => 'staff@company.com'],
            [
                'user_id' => 'STF-0001',
                'name' => 'Staff Karyawan',
                'password' => Hash::make('password'),
                'status' => 'aktif',
                'role_name' => 'anggota', // diubah dari 'staff' ke enum yang valid
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $staffUser = DB::table('users')->where('email', 'staff@company.com')->value('id');
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $staffRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $staffUser,
        ], []);

        // buat user super-admin dengan approval tertinggi
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@admin.com'],
            [
                'user_id' => 'SUPER-0001',
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'status' => 'aktif',
                'role_name' => 'pembina', // diubah dari 'super-admin' ke enum yang valid
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $superAdminUser = DB::table('users')->where('email', 'admin@admin.com')->value('id');
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $superAdminRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $superAdminUser,
        ], []);

        // buat data shift awal (Dihapus karena tabel shifts sudah tidak ada)
        /*
        DB::table('shifts')->insert([
            [
                'nama_shift' => 'shift pagi',
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'toleransi_menit' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_shift' => 'shift siang',
                'jam_masuk' => '14:00:00',
                'jam_keluar' => '22:00:00',
                'toleransi_menit' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_shift' => 'shift malam',
                'jam_masuk' => '22:00:00',
                'jam_keluar' => '06:00:00',
                'toleransi_menit' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        */

    }
}
