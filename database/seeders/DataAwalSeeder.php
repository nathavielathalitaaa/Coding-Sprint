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
        // buat 3 role untuk spatie permission: staff, supervisor, admin
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'supervisor',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'staff',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ambil role ids
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $supervisorRole = DB::table('roles')->where('name', 'supervisor')->first();
        $staffRole = DB::table('roles')->where('name', 'staff')->first();

        // buat user admin awal
        $adminUser = DB::table('users')->insertGetId([
            'user_id' => 'KH-0001',
            'name' => 'Administrator',
            'email' => 'admin@hris.test',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'role_name' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // assign role admin ke user admin
        DB::table('model_has_roles')->insert([
            'role_id' => $adminRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $adminUser,
        ]);

        // buat user supervisor demo
        $supervisorUser = DB::table('users')->insertGetId([
            'user_id' => 'KH-0002',
            'name' => 'Budi Supervisor',
            'email' => 'supervisor@hris.test',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'role_name' => 'supervisor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // assign role supervisor ke user supervisor
        DB::table('model_has_roles')->insert([
            'role_id' => $supervisorRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $supervisorUser,
        ]);

        // buat user staff demo
        $staffUser = DB::table('users')->insertGetId([
            'user_id' => 'KH-0003',
            'name' => 'Andi Staff',
            'email' => 'staff@hris.test',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'role_name' => 'staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // assign role staff ke user staff
        DB::table('model_has_roles')->insert([
            'role_id' => $staffRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $staffUser,
        ]);

        // buat data shift awal
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

        // buat data departemen tambahan
        DB::table('departments')->insert([
            [
                'department' => 'it',
                'head_of' => 'kosong',
                'phone_number' => '0',
                'email' => 'kosong@hris.test',
                'total_employee' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department' => 'keuangan',
                'head_of' => 'kosong',
                'phone_number' => '0',
                'email' => 'kosong@hris.test',
                'total_employee' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department' => 'sdm',
                'head_of' => 'kosong',
                'phone_number' => '0',
                'email' => 'kosong@hris.test',
                'total_employee' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department' => 'operasional',
                'head_of' => 'kosong',
                'phone_number' => '0',
                'email' => 'kosong@hris.test',
                'total_employee' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department' => 'pemasaran',
                'head_of' => 'kosong',
                'phone_number' => '0',
                'email' => 'kosong@hris.test',
                'total_employee' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
