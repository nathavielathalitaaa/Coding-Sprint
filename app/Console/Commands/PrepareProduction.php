<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PrepareProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:prepare-production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare the database for production by clearing test data and running the production seeder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('WARNING: Ini akan menghapus data dummy dan transaksi (surat, absensi, dokumen).');
        if ($this->confirm('Lanjutkan? (yes/no)', false)) {
            $this->info('Menyiapkan production data...');
            Artisan::call('db:seed', ['--class' => 'ProductionSeeder']);
            $this->info(Artisan::output());
            $this->info('Production setup selesai!');
        } else {
            $this->info('Dibatalkan.');
        }
    }
}
