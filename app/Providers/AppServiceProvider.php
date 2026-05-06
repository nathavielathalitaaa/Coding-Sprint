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

        // Share notification data ke layout master
        \Illuminate\Support\Facades\View::composer('layouts.master', function ($view) {
            if (!auth()->check()) {
                $view->with([
                    'notifCuti' => collect(),
                    'notifSurat' => collect(),
                    'totalNotif' => 0,
                ]);
                return;
            }

            $user = auth()->user();

            // Ambil notifikasi cuti yang masih menunggu
            $notifCuti = \App\Models\Leave::where('status', 'menunggu')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Ambil notifikasi surat berdasarkan JABATAN (bukan role)
            $notifSurat = collect();
            $jabatan = $user->profile?->jabatan;

            if ($jabatan) {
                $notifSurat = Surat::whereHas('approvals', function ($q) use ($jabatan) {
                    $q->where('jabatan', $jabatan)
                    ->where('status', 'waiting')
                    ->where('is_read', false)
                    ->where('document_type', 'LIKE', 'surat_%');
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            }

            // Hitung total notifikasi
            $totalCuti = \App\Models\Leave::where('status', 'menunggu')->count();
            $totalSurat = $notifSurat->count();
            $totalNotif = $totalCuti + $totalSurat;

            $view->with([
                'notifCuti' => $notifCuti,
                'notifSurat' => $notifSurat,
                'totalNotif' => $totalNotif,
            ]);
        });
    }
}
