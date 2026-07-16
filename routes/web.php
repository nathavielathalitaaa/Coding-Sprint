<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\SuratTurunanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// ── root redirect ─────────────────────────────────────
Route::get('/', fn() => redirect('/home'));
Route::get('/dashboard', fn() => redirect('/home'))->middleware('auth');

// ══════════════════════════════════════════════
// auth
// ══════════════════════════════════════════════
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'authenticate')->middleware('throttle:5,1');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('logout/page', 'logoutPage')->name('logout/page');
});

Route::get('/register', function() { abort(404); });
Route::post('/register', function() { abort(404); });

Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('forget-password', 'getEmail')->name('forget-password');
    Route::post('forget-password', 'postEmail');
});

Route::controller(ResetPasswordController::class)->group(function () {
    Route::get('reset-password/{token}', 'getPassword');
    Route::post('reset-password', 'updatePassword');
});

// ══════════════════════════════════════════════════════
// authenticated routes
// ══════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {

    // ══════════════════════════════════════════════
    // onboarding
    // ══════════════════════════════════════════════
    Route::controller(AccountController::class)->group(function () {
        Route::get('onboarding', 'showOnboarding')->name('onboarding');
        Route::post('onboarding/ttd', 'onboardingTtd')->name('onboarding.ttd');
        Route::post('onboarding/pin', 'onboardingPin')->name('onboarding.pin');
    });

    // ── all other routes (with onboarding + force_password middleware) ─
    Route::middleware(['onboarding', 'force_password'])->group(function () {

        // ── dashboard ──────────────────────────────────────
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/activity-log', [HomeController::class, 'activityLog'])->name('activity.log');

        // ── profil user ────────────────────────────────────
        Route::get('profile', [AccountController::class, 'showProfile'])->name('profile.show');
        Route::post('profile/update/{id?}', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/photo', [AccountController::class, 'updatePhoto'])->name('profile.photo');
        Route::delete('profile/photo', [AccountController::class, 'deletePhoto'])->name('profile.photo.delete');
        Route::post('profile/email', [AccountController::class, 'updateEmail'])->name('profile.email');
        Route::post('profile/password', [AccountController::class, 'updatePassword'])->name('profile.password');
        Route::post('profile/ttd', [AccountController::class, 'uploadTtd'])->name('profile.ttd');
        Route::post('profile/pin', [AccountController::class, 'setPin'])->name('profile.pin');
        Route::get('profile/ttd/preview', [AccountController::class, 'showTtd'])->name('profile.ttd.preview');

        Route::get('/ttd-preview/{userId}', function($userId) {
            $userModel = \App\Models\User::findOrFail($userId);
            $profile = $userModel->profile;

            $path = null;
            if ($profile) {
                if ($profile->signature_path) {
                    $path = storage_path('app/private/' . $profile->signature_path);
                    if (!file_exists($path)) {
                        $path = storage_path('app/public/' . $profile->signature_path);
                    }
                }

                if (!$path || !file_exists($path)) {
                    if ($profile->ttd_path) {
                        $path = storage_path('app/private/' . $profile->ttd_path);
                        if (!file_exists($path)) {
                            $path = storage_path('app/private/private/' . $profile->ttd_path);
                        }
                        if (!file_exists($path)) {
                            $path = storage_path('app/public/' . $profile->ttd_path);
                        }
                    }
                }
            }

            if (!$path || !file_exists($path)) {
                if ($userModel->ttd_path) {
                    $path = storage_path('app/private/' . $userModel->ttd_path);
                    if (!file_exists($path)) {
                        $path = storage_path('app/private/private/' . $userModel->ttd_path);
                    }
                    if (!file_exists($path)) {
                        $path = storage_path('app/public/' . $userModel->ttd_path);
                    }
                }
            }

            if (!$path || !file_exists($path)) abort(404);

            $mime = str_ends_with($path, '.png') ? 'image/png' : 'image/jpeg';
            return response()->file($path, ['Content-Type' => $mime]);
        })->name('ttd.preview.user')->middleware('auth');

        // ── digital signature ──────────────────────────────
        Route::post('profile/signature/{id?}', [AccountController::class, 'uploadSignature'])->name('profile.signature.upload');
        Route::delete('profile/signature/{id?}', [AccountController::class, 'deleteSignature'])->name('profile.signature.delete');

        // ── profil ─────────────────────────────────────────
        Route::get('page/account/{user_id}', [AccountController::class, 'profileDetail']);

        // ── search ─────────────────────────────────────────
        Route::get('search', [SearchController::class, 'cari'])->name('search');

        // ══════════════════════════════════════════════
        // Admin panel (role: admin)
        // ══════════════════════════════════════════════
        Route::prefix('admin')->middleware('role:admin|super-admin')->group(function () {

            // system monitor
            Route::get('system/monitor', [\App\Http\Controllers\SystemMonitorController::class, 'index'])->name('system/monitor');
            Route::get('system/monitor/archive-manager', [\App\Http\Controllers\SystemMonitorController::class, 'archiveManager'])->name('system/monitor/archive-manager');
            Route::post('system/monitor/archive', [\App\Http\Controllers\SystemMonitorController::class, 'archiveDocuments'])->name('system/monitor/archive');

            // ── settings dokumen ───────────────────────────
            Route::controller(\App\Http\Controllers\DocumentSettingController::class)
                ->prefix('settings')
                ->group(function () {
                    Route::get('document', 'index')->name('users.settings.document');
                    Route::post('document', 'update')->name('users.settings.document.update');
                    Route::post('document/logo', 'uploadLogo')->name('users.settings.document.logo');
                });

            // ── Master Data ────────────────────────────────
            Route::controller(\App\Http\Controllers\MasterDataController::class)
                ->prefix('settings')
                ->group(function () {
                    Route::get('master', 'index')->name('users.settings.master');
                    Route::post('position', 'storePosition')->name('users.settings.position.store');
                    Route::put('position/{id}', 'updatePosition')->name('users.settings.position.update');
                    Route::delete('position/{id}', 'destroyPosition')->name('users.settings.position.destroy');
                    Route::post('user-type', 'storeUserType')->name('users.settings.usertype.store');
                    Route::put('user-type/{id}', 'updateUserType')->name('users.settings.usertype.update');
                    Route::delete('user-type/{id}', 'destroyUserType')->name('users.settings.usertype.destroy');
                    Route::post('role-type', 'storeRoleType')->name('users.settings.roletype.store');
                    Route::put('role-type/{id}', 'updateRoleType')->name('users.settings.roletype.update');
                    Route::delete('role-type/{id}', 'destroyRoleType')->name('users.settings.roletype.destroy');
                });
        });

        // ══════════════════════════════════════════════
        // Kelola Organisasi (role: admin)
        // ══════════════════════════════════════════════
        Route::middleware('role:admin|super-admin')->group(function () {
            Route::resource('organisasi', OrganisasiController::class)->except(['edit', 'update', 'destroy']);
            Route::post('organisasi/{organisasi}/members', [OrganisasiController::class, 'addMember'])->name('organisasi.members.add');
            Route::delete('organisasi/{organisasi}/members/{member}', [OrganisasiController::class, 'removeMember'])->name('organisasi.members.remove');
            Route::post('organisasi/{organisasi}/komisi', [OrganisasiController::class, 'createKomisi'])->name('organisasi.komisi.store');
            Route::post('komisi/{komisi}/members', [OrganisasiController::class, 'addKomisiMember'])->name('komisi.members.add');
            Route::delete('komisi/{komisi}/members/{member}', [OrganisasiController::class, 'removeKomisiMember'])->name('komisi.members.remove');
        });

        // ══════════════════════════════════════════════
        // surat
        // ══════════════════════════════════════════════
        Route::controller(SuratController::class)
        ->prefix('surat')
        ->name('surat.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('inbox-admin', 'inboxAdmin')->name('inbox_admin');
            Route::post('{surat}/verifikasi', 'verifikasiAdmin')->name('verifikasi_admin');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');

            Route::get('ttd-mode', 'getTtdMode')->name('ttd-mode');
            Route::get('ttd-preview/{jabatan}', 'getTtdPreview')->name('ttd-preview');

            // ── Endpoint validasi real-time (AJAX, non-blocking) ──
            Route::get('cek-duplikat', 'cekDuplikat')->name('cek-duplikat');
            Route::get('cek-konflik',  'cekKonflik')->name('cek-konflik');

            Route::get('{surat}', 'show')->name('show');
            Route::get('{surat}/edit', 'edit')->name('edit');
            Route::put('{surat}', 'update')->name('update');
            Route::get('{surat}/download', 'download')->name('download');
            Route::delete('{surat}', 'destroy')->name('destroy');

            // approve & reject
            Route::post('{surat}/approve', 'approve')->name('approve');
            Route::post('{surat}/reject', 'reject')->name('reject');

            // regenerate cover pdf
            Route::get('{id}/regenerate-final', function($id) {
                $surat = \App\Models\Surat::findOrFail($id);
                $coverService = app(\App\Services\ApprovalCoverService::class);
                $stampService = app(\App\Services\PdfStampService::class);
                try {
                    $documentType = 'surat_' . $surat->jenis_surat;
                    $step = \App\Models\ApprovalStep::where('document_type', $documentType)->first();
                    $ttdMode = $step?->ttd_mode ?? 'append';

                    if ($ttdMode === 'stamp') {
                        $path = $stampService->stamp($surat);
                        $surat->update(['final_pdf_path' => $path]);
                    } else {
                        $path = $coverService->generateCover($surat);
                        $surat->update(['cover_pdf_path' => $path]);
                        $finalPath = $coverService->processMerge($surat);
                        if ($finalPath) {
                            $surat->update(['final_pdf_path' => $finalPath]);
                            $path = $finalPath;
                        }
                    }
                    return response()->json(['success' => true, 'path' => $path]);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            })->name('regenerate-final');
        });

        // ══════════════════════════════════════════════
        // surat turunan (nested di bawah surat induk)
        // Hanya bisa diakses kalau surat->status === 'approved_owner'
        // (guard dijalankan di dalam controller)
        // ══════════════════════════════════════════════
        Route::prefix('surat/{surat}/turunan')
            ->name('surat.turunan.')
            ->controller(SuratTurunanController::class)
            ->group(function () {
                // Daftar semua turunan milik surat induk
                Route::get('/',        'index')->name('index');

                // Form pilih template + signer
                Route::get('/create',  'create')->name('create');

                // Proses generate (bisa multi-template sekaligus)
                Route::post('/',       'store')->name('store');

                // Tanda tangan oleh signer yang ditugaskan
                Route::post('/{suratTurunan}/signer/{signer}/sign', 'sign')->name('sign');

                // Download PDF final
                Route::get('/{suratTurunan}/download', 'download')->name('download');
            });

        // ══════════════════════════════════════════════
        // surat type management (role: admin)
        // ══════════════════════════════════════════════
        Route::middleware('role:admin|super-admin')->prefix('surat-type')->name('surat-type.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuratTypeController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\SuratTypeController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\SuratTypeController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\SuratTypeController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\SuratTypeController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\SuratTypeController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [\App\Http\Controllers\SuratTypeController::class, 'toggle'])->name('toggle');
        });

        // ══════════════════════════════════════════════
        // surat turunan template management (role: admin)
        // ══════════════════════════════════════════════
        Route::middleware('role:admin|super-admin')
            ->prefix('admin/surat-turunan-template')
            ->name('surat-turunan-template.')
            ->controller(\App\Http\Controllers\SuratTurunanTemplateController::class)
            ->group(function () {
                Route::get('/',           'index')->name('index');
                Route::get('/{suratTurunanTemplate}/edit',   'edit')->name('edit');
                Route::put('/{suratTurunanTemplate}',        'update')->name('update');
                Route::patch('/{suratTurunanTemplate}/toggle', 'toggle')->name('toggle');
            });

        // ── TAHAP 3: Disposisi Akhir (PIC assignment) ──
        Route::controller(\App\Http\Controllers\DisposisiAkhirController::class)
            ->group(function () {
                Route::get('disposisi-akhir', 'index')->name('disposisi-akhir.index');
                Route::post('disposisi-akhir/{surat}/assign', 'assign')->name('disposisi-akhir.assign');
            });

        // ── TAHAP 4: Monitoring Pelaksanaan (PIC area) ──
        Route::controller(\App\Http\Controllers\PelaksanaanController::class)
            ->group(function () {
                Route::get('pelaksanaan-saya', 'index')->name('pelaksanaan.index');
                Route::post('pelaksanaan/{surat}/progress', 'updateProgress')->name('pelaksanaan.progress');
                Route::post('pelaksanaan/{surat}/selesai', 'selesai')->name('pelaksanaan.selesai');
            });

        // ── TAHAP 5 & 6: LPJ Management & Verification ──
        Route::controller(\App\Http\Controllers\LpjController::class)
            ->group(function () {
                Route::get('surat/{surat}/lpj/create', 'create')->name('lpj.create');
                Route::post('surat/{surat}/lpj', 'store')->name('lpj.store');
                Route::get('surat/{surat}/lpj/edit', 'edit')->name('lpj.edit');
                Route::put('surat/{surat}/lpj', 'update')->name('lpj.update');
                Route::get('surat/{surat}/lpj', 'show')->name('lpj.show');

                Route::get('lpj-verifikasi', 'indexVerifikasi')->name('lpj.verifikasi.index');
                Route::post('surat/{surat}/lpj/verify', 'verify')->name('lpj.verify');
            });

        // ── TAHAP 7: Database Arsip ──
        Route::controller(\App\Http\Controllers\ArsipController::class)
            ->group(function () {
                Route::get('database-arsip', 'index')->name('arsip.index');
            });

    }); // end middleware('onboarding')

}); // end middleware('auth')
