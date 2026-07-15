<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Simple view routes for UI pages (development, pixel-perfect views)
Route::get('/', function() { return view('dashboard.index'); });
Route::get('/dashboard', function() { return view('dashboard.index'); });
Route::get('/surat', function() { return view('surat.index'); });
Route::get('/surat/create', function() { return view('surat.create'); });
Route::get('/persetujuan', function() { return view('approval.index'); });
Route::get('/daftar-surat', function() { return view('daftar-surat.index'); });

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
            $profile = \App\Models\EmployeeProfile::where('user_id', $userId)->firstOrFail();
            
            $path = null;
            if ($profile->signature_path) {
                // Check in private storage first (new secure approach)
                $path = storage_path('app/private/' . $profile->signature_path);
                if (!file_exists($path)) {
                    // Fallback to public storage for legacy files
                    $path = storage_path('app/public/' . $profile->signature_path);
                }
            } 
            
            if (!$path || !file_exists($path)) {
                if ($profile->ttd_path) {
                    // Check in private storage (onboarding/manual)
                    // Check both storage/app/private/ttd/... and storage/app/private/private/ttd/...
                    $path = storage_path('app/private/' . $profile->ttd_path);
                    if (!file_exists($path)) {
                        $path = storage_path('app/private/private/' . $profile->ttd_path);
                    }
                    if (!file_exists($path)) {
                        // Also check public storage just in case
                        $path = storage_path('app/public/' . $profile->ttd_path);
                    }
                }
            }

            if (!$path || !file_exists($path)) abort(404);
            
            $mime = str_ends_with($path, '.png') ? 'image/png' : 'image/jpeg';
            return response()->file($path, ['Content-Type' => $mime]);
        })->name('ttd.preview.user')->middleware('auth');

        // ── digital signature (new public storage approach) ──
        Route::post('profile/signature/{id?}', [AccountController::class, 'uploadSignature'])->name('profile.signature.upload');
        Route::delete('profile/signature/{id?}', [AccountController::class, 'deleteSignature'])->name('profile.signature.delete');

        // ── profil ─────────────────────────────────────────
        Route::get('page/account/{user_id}', [AccountController::class, 'profileDetail']);

        // ── search ─────────────────────────────────────────
        Route::get('search', [SearchController::class, 'cari'])->name('search');

        // ══════════════════════════════════════════════
        // hr management (role: hr)
        // ══════════════════════════════════════════════
    Route::prefix('hr')->group(function () {

        Route::controller(HRController::class)->group(function () {
            // karyawan
            Route::get('employee/list', 'employeeList')->name('hr/employee/list');
            Route::post('employee/save', 'employeeSaveRecord')->name('hr/employee/save');
            Route::post('employee/update', 'employeeUpdateRecord')->name('hr/employee/update');
            Route::post('employee/delete', 'employeeDeleteRecord')->name('hr/employee/delete');
            Route::get('employee/show/{id}', 'showEmployee')->name('hr/employee/show');
            Route::get('employee/{id}/edit', 'editEmployee')->name('hr/employee/edit');
            // import karyawan
            Route::post('employee/import', 'importKaryawan')->name('hr/employee/import');
            Route::get('employee/template', 'downloadTemplate')->name('hr/employee/template');



        });
        
        // system monitor
        Route::get('system/monitor', [\App\Http\Controllers\SystemMonitorController::class, 'index'])->name('hr/system/monitor');
        Route::get('system/monitor/archive-manager', [\App\Http\Controllers\SystemMonitorController::class, 'archiveManager'])->name('hr/system/monitor/archive-manager');
        Route::post('system/monitor/archive', [\App\Http\Controllers\SystemMonitorController::class, 'archiveDocuments'])->name('hr/system/monitor/archive');

        // ── absensi (unified module) ──────────────────────
        Route::controller(AbsensiController::class)->group(function () {
            Route::get('absensi/page', 'index')->name('hr/absensi/page');
            Route::get('absensi/export/excel', 'exportExcel')->name('hr/absensi/export/excel');
            Route::get('absensi/export/pdf', 'exportPdf')->name('hr/absensi/export/pdf');
            Route::delete('absensi/{id}', 'destroy')->name('hr/absensi/delete');
            // Tab 2 – AI import (JSON endpoints)
            Route::post('absensi/process-ai', 'processAI')->name('hr/absensi/process-ai');
            Route::post('absensi/confirm-import', 'confirmImport')->name('hr/absensi/confirm-import');
            Route::post('absensi/import/map', 'mapFingerprint')->name('hr/absensi/import/map');
            // Tab 3 – rekap data (JSON)
            Route::get('absensi/rekap-data', 'rekapData')->name('hr/absensi/rekap-data');
        });


        // ══════════════════════════════════════════════
        // settings dokumen
        // ══════════════════════════════════════════════
        Route::controller(\App\Http\Controllers\DocumentSettingController::class)
            ->prefix('settings')
            ->middleware('role:hr|super-admin')
            ->group(function () {
                Route::get('document', 'index')->name('hr.settings.document');
                Route::post('document', 'update')->name('hr.settings.document.update');
                Route::post('document/logo', 'uploadLogo')->name('hr.settings.document.logo');
            });

        // Master Data Management
        Route::controller(\App\Http\Controllers\MasterDataController::class)
            ->prefix('settings')
            ->middleware('role:hr')
            ->group(function () {
                Route::get('master', 'index')->name('hr.settings.master');
                
                // Position
                Route::post('position', 'storePosition')->name('hr.settings.position.store');
                Route::put('position/{id}', 'updatePosition')->name('hr.settings.position.update');
                Route::delete('position/{id}', 'destroyPosition')->name('hr.settings.position.destroy');
                
                // User Type
                Route::post('user-type', 'storeUserType')->name('hr.settings.usertype.store');
                Route::put('user-type/{id}', 'updateUserType')->name('hr.settings.usertype.update');
                Route::delete('user-type/{id}', 'destroyUserType')->name('hr.settings.usertype.destroy');
                
                // Role Type
                Route::post('role-type', 'storeRoleType')->name('hr.settings.roletype.store');
                Route::put('role-type/{id}', 'updateRoleType')->name('hr.settings.roletype.update');
                Route::delete('role-type/{id}', 'destroyRoleType')->name('hr.settings.roletype.destroy');
            });

    }); // end prefix hr

        // ══════════════════════════════════════════════
        // surat (role: staff, supervisor, hr)
        // ══════════════════════════════════════════════
       Route::controller(SuratController::class)
    ->prefix('surat')
    ->name('surat.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');

        Route::get('ttd-mode', 'getTtdMode')->name('ttd-mode');
        Route::get('ttd-preview/{jabatan}', 'getTtdPreview')->name('ttd-preview');

        Route::get('{surat}', 'show')->name('show');
        Route::get('{surat}/edit', 'edit')->name('edit');
        Route::put('{surat}', 'update')->name('update');
        Route::get('{surat}/download', 'download')->name('download');
        Route::delete('{surat}', 'destroy')->name('destroy');

        Route::post('{surat}/approve', 'approve')->name('approve');
        Route::post('{surat}/reject', 'reject')->name('reject');

        Route::get('{id}/regenerate-final', function ($id) {
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

                return response()->json([
                    'success' => true,
                    'path' => $path,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        })->name('regenerate-final');
    });

        // ══════════════════════════════════════════════
        // surat type management (role: hr)
        // ══════════════════════════════════════════════
        // Ganti menjadi middleware permission
        // Ganti baris 254-256 di web.php menjadi ini:

    }); // end middleware('onboarding')

}); // end middleware('auth')

