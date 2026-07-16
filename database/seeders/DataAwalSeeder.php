<?php

namespace Database\Seeders;

use DB;
use Hash;
use Illuminate\Database\Seeder;

class DataAwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Setup roles, permissions, dan user awal untuk SIMORA.
     */
    public function run(): void
    {
        // ── Buat Roles Spatie Permission ───────────────────────────────
        // Role dasar sistem
        $roles = [
            ['name' => 'admin',           'guard_name' => 'web'],
            ['name' => 'guru',            'guard_name' => 'web'],
            ['name' => 'anggota',         'guard_name' => 'web'],
            // Role global (tidak terikat Organisasi, approve semua surat step akhir)
            ['name' => 'pengawas_pusat',  'guard_name' => 'web'],
            ['name' => 'kepala_sekolah',  'guard_name' => 'web'],
            // Super admin (bypass semua approval)
            ['name' => 'super-admin',     'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ── Ambil Role IDs ──────────────────────────────────────────────
        $adminRole          = DB::table('roles')->where('name', 'admin')->first();
        $guruRole           = DB::table('roles')->where('name', 'guru')->first();
        $anggotaRole        = DB::table('roles')->where('name', 'anggota')->first();
        $pengawasPusatRole  = DB::table('roles')->where('name', 'pengawas_pusat')->first();
        $kepalaSekolahRole  = DB::table('roles')->where('name', 'kepala_sekolah')->first();
        $superAdminRole     = DB::table('roles')->where('name', 'super-admin')->first();

        // ── Buat User Admin (Super Admin) ───────────────────────────────
        $adminId = DB::table('users')->insertGetId([
            'user_id'    => 'ADMIN-001',
            'name'       => 'Admin SIMORA',
            'email'      => 'admin@skomda.com',
            'password'   => Hash::make('password'),
            'status'     => 'aktif',
            'role_name'  => 'super-admin',
            'must_change_password' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Berikan role super-admin agar memiliki akses penuh
        DB::table('model_has_roles')->insert([
            'role_id'    => $superAdminRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $adminId,
        ]);
        // Juga pertahankan role admin untuk kompatibilitas
        DB::table('model_has_roles')->insert([
            'role_id'    => $adminRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $adminId,
        ]);

        // ── Buat User Pengawas Pusat ────────────────────────────────────
        $pengawasPusatId = DB::table('users')->insertGetId([
            'user_id'    => 'GURU-001',
            'name'       => 'Pengawas Pusat SMK',
            'email'      => 'pengawas.pusat@skomda.com',
            'password'   => Hash::make('password'),
            'status'     => 'aktif',
            'role_name'  => 'pengawas_pusat',
            'must_change_password' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id'    => $pengawasPusatRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $pengawasPusatId,
        ]);

        // ── Buat User Kepala Sekolah ────────────────────────────────────
        $kepalaSekolahId = DB::table('users')->insertGetId([
            'user_id'    => 'GURU-002',
            'name'       => 'Kepala Sekolah SMK Telkom Sidoarjo',
            'email'      => 'kepsek@skomda.com',
            'password'   => Hash::make('password'),
            'status'     => 'aktif',
            'role_name'  => 'kepala_sekolah',
            'must_change_password' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id'    => $kepalaSekolahRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $kepalaSekolahId,
        ]);

        // ── Buat User Guru Demo (bisa jadi Pembina/Pengawas via OrganisasiMember) ──
        $guru1Id = DB::table('users')->insertGetId([
            'user_id'    => 'GURU-003',
            'name'       => 'Bapak Pembina OSIS',
            'email'      => 'pembina.osis@skomda.com',
            'password'   => Hash::make('password'),
            'status'     => 'aktif',
            'role_name'  => 'guru',
            'must_change_password' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id'    => $guruRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $guru1Id,
        ]);

        $guru2Id = DB::table('users')->insertGetId([
            'user_id'    => 'GURU-004',
            'name'       => 'Ibu Pembina MPK',
            'email'      => 'pembina.mpk@skomda.com',
            'password'   => Hash::make('password'),
            'status'     => 'aktif',
            'role_name'  => 'guru',
            'must_change_password' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id'    => $guruRole->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $guru2Id,
        ]);

        // ── Buat User Anggota Demo ──────────────────────────────────────
        $anggotaUsers = [
            ['user_id' => 'ANT-001', 'name' => 'Ketua OSIS',       'email' => 'ketua.osis@skomda.com'],
            ['user_id' => 'ANT-002', 'name' => 'BPH OSIS 1',       'email' => 'bph.osis1@skomda.com'],
            ['user_id' => 'ANT-003', 'name' => 'Ketua MPK',        'email' => 'ketua.mpk@skomda.com'],
            ['user_id' => 'ANT-004', 'name' => 'BPH MPK 1',        'email' => 'bph.mpk1@skomda.com'],
            ['user_id' => 'ANT-005', 'name' => 'Anggota ROHIS',    'email' => 'bph.rohis1@skomda.com'],
        ];

        $anggotaIds = [];
        foreach ($anggotaUsers as $u) {
            $uid = DB::table('users')->insertGetId(array_merge($u, [
                'password'   => Hash::make('password'),
                'status'     => 'aktif',
                'role_name'  => 'anggota',
                'must_change_password' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            DB::table('model_has_roles')->insert([
                'role_id'    => $anggotaRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id'   => $uid,
            ]);
            $anggotaIds[$u['user_id']] = $uid;
        }
    }
}
