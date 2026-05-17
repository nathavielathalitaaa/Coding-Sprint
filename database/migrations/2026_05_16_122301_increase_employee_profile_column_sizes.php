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
            $table->string('nik', 500)->nullable()->change();
            $table->string('no_kk', 500)->nullable()->change();
            $table->string('npwp', 500)->nullable()->change();
            $table->string('bpjs_kesehatan', 500)->nullable()->change();
            $table->string('bpjs_ketenagakerjaan', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->change();
            $table->string('no_kk', 20)->nullable()->change();
            $table->string('npwp', 20)->nullable()->change();
            $table->string('bpjs_kesehatan', 25)->nullable()->change();
            $table->string('bpjs_ketenagakerjaan', 25)->nullable()->change();
        });
    }
};
