<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_type_users')) {
            DB::table('role_type_users')->truncate();
            DB::table('role_type_users')->insert([
                ['role_type' => 'anggota', 'created_at' => now(), 'updated_at' => now()],
                ['role_type' => 'BPH', 'created_at' => now(), 'updated_at' => now()],
                ['role_type' => 'pembina', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        foreach (['anggota', 'BPH', 'pembina'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
                ['guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]
            );
        }

        if (Schema::hasTable('users')) {
            DB::table('users')
                ->whereNotIn('role_name', ['anggota', 'BPH', 'pembina'])
                ->update(['role_name' => 'anggota']);

            DB::statement("ALTER TABLE `users` MODIFY `role_name` ENUM('anggota','BPH','pembina') NOT NULL DEFAULT 'anggota'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE `users` MODIFY `role_name` VARCHAR(255) NULL");
        }

        if (Schema::hasTable('role_type_users')) {
            DB::table('role_type_users')->truncate();
            DB::table('role_type_users')->insert([
                ['role_type' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
                ['role_type' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()],
                ['role_type' => 'Normal User', 'created_at' => now(), 'updated_at' => now()],
                ['role_type' => 'Client', 'created_at' => now(), 'updated_at' => now()],
                ['role_type' => 'Employee', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        DB::table('roles')->whereIn('name', ['anggota', 'BPH', 'pembina'])->delete();
    }
};
