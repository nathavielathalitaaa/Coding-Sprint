<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\SuratController;

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware'=>'auth'],function()
{
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
});

Auth::routes();

Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // -----------------------------login----------------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
        Route::get('logout/page', 'logoutPage')->name('logout/page');
    });

    // ------------------------------ register ----------------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register','storeUser')->name('register');    
    });

    // ----------------------------- forget password ----------------------------//
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('forget-password', 'getEmail')->name('forget-password');
        Route::post('forget-password', 'postEmail')->name('forget-password');    
    });

    // ----------------------------- reset password -----------------------------//
    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('reset-password/{token}', 'getPassword');
        Route::post('reset-password', 'updatePassword');    
    });
});

Route::group(['namespace' => 'App\Http\Controllers'],function()
{
    // -------------------------- main dashboard ----------------------//
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->middleware('auth')->name('home');
    });

    // -------------------------- pages ----------------------//
    Route::controller(AccountController::class)->group(function () {
        Route::get('page/account/{user_id}', 'profileDetail')->middleware('auth');
    });

    // -------------------------- search ----------------------//
    Route::get('search', [App\Http\Controllers\SearchController::class, 'cari'])->name('search')->middleware('auth');

    // -------------------------- hr ----------------------//
    Route::middleware('auth')->prefix('hr/')->group(function () {
        Route::controller(HRController::class)->group(function () {
            Route::get('employee/list', 'employeeList')->name('hr/employee/list');
            Route::post('employee/save', 'employeeSaveRecord')->name('hr/employee/save');
            Route::post('employee/update', 'employeeUpdateRecord')->name('hr/employee/update');
            Route::post('employee/delete', 'employeeDeleteRecord')->name('hr/employee/delete');
            
            Route::get('holidays/page', 'holidayPage')->name('hr/holidays/page');
            Route::post('holidays/save', 'holidaySaveRecord')->name('hr/holidays/save');
            Route::post('holidays/delete', 'holidayDeleteRecord')->name('hr/holidays/delete');
            
            Route::get('leave/employee/page', 'leaveEmployee')->name('hr/leave/employee/page');
            Route::get('create/leave/employee/page', 'createLeaveEmployee')->name('hr/create/leave/employee/page');
            Route::post('create/leave/employee/save', 'saveRecordLeave')->name('hr/create/leave/employee/save');
            Route::get('view/detail/leave/employee/{staff_id}', 'viewDetailLeave');
            
            Route::get('leave/hr/page', 'leaveHR')->name('hr/leave/hr/page');
            Route::post('leave/approve', 'approveLeave')->name('hr/leave/approve');
            Route::post('leave/reject', 'rejectLeave')->name('hr/leave/reject');
            Route::get('attendance/page', 'attendance')->name('hr/attendance/page');
            Route::get('create/leave/hr/page', 'createLeaveHR')->name('hr/create/leave/hr/page');

            Route::post('get/information/leave', 'getInformationLeave')->name('hr/get/information/leave');
        
            Route::get('attendance/main/page', 'attendanceMain')->name('hr/attendance/main/page');
            Route::get('department/page', 'department')->name('hr/department/page');
            Route::post('department/save', 'saveRecorddepartment')->name('hr/department/save');
            Route::post('department/delete', 'deleteRecorddepartment')->name('hr/department/delete');
        });

        // ---- absensi ----
        Route::controller(AbsensiController::class)->group(function () {
            Route::get('absensi/page', 'index')->name('hr/absensi/page');
            Route::post('absensi/store', 'store')->name('hr/absensi/store');
            Route::post('absensi/clock-in', 'clockIn')->name('hr/absensi/clock-in');
            Route::post('absensi/clock-out', 'clockOut')->name('hr/absensi/clock-out');
        });

        // ---- shift ----
        Route::controller(ShiftController::class)->group(function () {
            Route::get('shift/page', 'index')->name('hr/shift/page');
            Route::post('shift/store', 'store')->name('hr/shift/store');
            Route::post('shift/delete', 'destroy')->name('hr/shift/delete');
            Route::get('shift/jadwal', 'jadwal')->name('hr/shift/jadwal');
            Route::post('shift/jadwal/store', 'simpanJadwal')->name('hr/shift/jadwal/store');
        });

        // ---- penggajian ----
        Route::controller(PenggajianController::class)->group(function () {
            Route::get('penggajian/page', 'index')->name('hr/penggajian/page');
            Route::post('penggajian/generate', 'generate')->name('hr/penggajian/generate');
            Route::get('penggajian/show/{id}', 'show')->name('hr/penggajian/show');
            Route::post('penggajian/bayar', 'bayar')->name('hr/penggajian/bayar');
        });
    });

    // ---- surat (letter management system) ----
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::controller(SuratController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('{surat}', 'show')->name('show');
            Route::get('{surat}/edit', 'edit')->name('edit');
            Route::put('{surat}', 'update')->name('update');
            Route::get('{surat}/download', 'download')->name('download');
            Route::post('{surat}/approve-supervisor', 'approveSupervisor')->name('approve-supervisor');
            Route::post('{surat}/reject-supervisor', 'rejectSupervisor')->name('reject-supervisor');
            Route::post('{surat}/approve-owner', 'approveOwner')->name('approve-owner');
            Route::post('{surat}/reject-owner', 'rejectOwner')->name('reject-owner');
        });
    });
});