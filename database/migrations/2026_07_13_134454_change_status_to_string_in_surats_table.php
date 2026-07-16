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
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            Schema::table('surats', function (Blueprint $table) {
                $table->string('status', 50)->default('submitted')->change();
            });
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE surats MODIFY status VARCHAR(50) DEFAULT 'submitted'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            //
        });
    }
};
