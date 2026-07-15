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
        // buat 3 role untuk spatie permission: staff, supervisor, hr
        DB::table('roles')->insert([
            [
                'name' => 'BPH',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anggota',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pembina',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ambil role ids
        $bphRole = DB::table('roles')->where('name', 'BPH')->first();
        $anggotaRole = DB::table('roles')->where('name', 'Anggota')->first();

        // buat user hr demo
        $bphUser = DB::table('users')->insertGetId([
            'user_id' => 'BPH-0001',
            'name' => 'Admin BPH',
            'email' => 'bph@company.com',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'role_name' => 'BPH',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // assign role BPH ke user bph
        DB::table('model_has_roles')->insert([
            'role_id' => $bphRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $bphUser,
        ]);

        // buat user anggota demo
        $anggotaUser = DB::table('users')->insertGetId([
            'user_id' => 'ANG-0001',
            'name' => 'Anggota Demo',
            'email' => 'anggota@company.com',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'role_name' => 'Anggota',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // assign role Anggota ke user anggota
        DB::table('model_has_roles')->insert([
            'role_id' => $anggotaRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $anggotaUser,
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

    }
}
