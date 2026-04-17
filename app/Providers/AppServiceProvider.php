<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Surat;
use App\Policies\SuratPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policy untuk Surat
        \Illuminate\Support\Facades\Gate::policy(Surat::class, SuratPolicy::class);

        // bagikan data notifikasi ke semua view yang menggunakan layout master
        \Illuminate\Support\Facades\View::composer('layouts.master', function ($view) {
            // hanya ambil data kalau user sudah login
            if (auth()->check()) {
                // ambil 5 pengajuan cuti yang masih menunggu
                $notifCuti = \App\Models\Leave::where('status', 'Pending')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

                // ambil surat yang perlu approval sesuai role
                $notifSurat = collect();
                $user = auth()->user();
                
                if ($user->hasRole('supervisor')) {
                    // supervisor: surat status submitted
                    $notifSurat = Surat::where('status', 'submitted')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                } elseif ($user->hasRole('admin')) {
                    // admin: surat status approved_supervisor
                    $notifSurat = Surat::where('status', 'approved_supervisor')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                }

                // hitung total untuk badge angka (cuti + surat)
                $totalCuti = \App\Models\Leave::where('status', 'Pending')->count();
                $totalSurat = $notifSurat->count();
                $totalNotif = $totalCuti + $totalSurat;

                $view->with('notifCuti', $notifCuti);
                $view->with('notifSurat', $notifSurat);
                $view->with('totalNotif', $totalNotif);
            } else {
                $view->with('notifCuti', collect());
                $view->with('notifSurat', collect());
                $view->with('totalNotif', 0);
            }
        });
    }
}
