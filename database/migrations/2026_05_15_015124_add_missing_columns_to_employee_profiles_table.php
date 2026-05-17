<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('departemen')->nullable()->after('user_id');
            $table->string('posisi')->nullable()->after('departemen');
            $table->string('status')->nullable()->default('Active')->after('posisi');
            $table->string('no_telepon')->nullable()->after('pendidikan_terakhir');
            $table->string('jenis_kelamin')->nullable()->after('jumlah_anak');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tgl_lahir')->nullable()->after('tempat_lahir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'departemen',
                'posisi',
                'status',
                'no_telepon',
                'jenis_kelamin',
                'tempat_lahir',
                'tgl_lahir'
            ]);
        });
    }
};
