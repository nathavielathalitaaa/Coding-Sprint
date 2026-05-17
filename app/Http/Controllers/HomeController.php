<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\DocumentApproval;
use App\Models\Surat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // pastikan semua method di controller ini cuma bs diakses user yg udah login
    public function __construct()
    {
        $this->middleware('auth');
    }

    // method utama dashboard: nampilin data berbeda berdasarkan role user (hr/supervisor/hod/staff), termasuk statistik absensi, surat, & chart
    public function index()
    {
        $user = auth()->user();

        // data dasar yg dikirim ke semua role: nama role yg diformat & pesan selamat datang
        $data = [
            'userRoleName'    => match(true) {
                $user->hasRole('hr')                 => 'HR',
                $user->hasRole('supervisor')          => 'Supervisor',
                $user->hasRole('staff')               => 'Staff',
                $user->hasRole('head_of_department') => 'Head of Department',
                default                               => 'Karyawan'
            },
            'userDisplayName' => 'Selamat datang kembali',
        ];

        // ── hr: statistik penuh ───────────────────────────────
        // klo user hr, tambahin data statistik lengkap: total karyawan, hadir bulan ini, total lembur, chart absensi, & count surat menunggu/selesai
        if ($user->hasRole('hr')) {
            $data = array_merge($data, [
                'totalKaryawan'       => User::where('status', 'aktif')->count(),
                'hadirBulanIni'       => Absensi::where('tanggal', now()->format('Y-m-01'))->where('status', 'hadir')->count(),
                'totalJamLembur'      => $this->getTotalJamLembur(),
                'chartAbsensi'        => $this->getChartAbsensi(),
                'suratMenungguCount'  => DocumentApproval::where('status', 'waiting')->where('document_type', 'LIKE', 'surat_%')->count(),
                'suratSelesaiHariIni' => Surat::where('status', 'approved_owner')->whereDate('updated_at', now()->format('Y-m-d'))->count(),
                'recentActivities'    => \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->take(5)->get(),
            ]);
        }

        // ── supervisor: monitoring + approval ────────────────
        // klo user supervisor, tambahin data monitoring tim & list surat yg butuh approval berdasarkan jabatan atau assigned user
        elseif ($user->hasRole('supervisor')) {
            $jabatan = $user->profile?->jabatan;
            $data = array_merge($data, [
                'totalKaryawan'      => User::where('status', 'aktif')->count(),
                'hadirBulanIni'      => Absensi::where('tanggal', now()->format('Y-m-01'))->where('status', 'hadir')->count(),
                'suratMenungguCount' => DocumentApproval::where('status', 'waiting')
                                            ->where(function($q) use ($jabatan, $user) {
                                                $q->where('jabatan', $jabatan)
                                                  ->orWhere('assigned_user_id', $user->id);
                                            })
                                            ->where('document_type', 'LIKE', 'surat_%')
                                            ->count(),
                'suratMenungguList'  => Surat::whereHas('approvals', function($q) use ($jabatan, $user) {
                                                $q->where(function($q2) use ($jabatan, $user) {
                                                    $q2->where('jabatan', $jabatan)
                                                       ->orWhere('assigned_user_id', $user->id);
                                                })->where('status', 'waiting');
                                            })
                                            ->with('user')
                                            ->orderBy('created_at', 'desc')
                                            ->take(5)
                                            ->get(),
            ]);
        }

        // ── head_of_department: monitoring + approval ─────────
        // klo user hod, logic mirip supervisor: monitoring tim & approval surat berdasarkan jabatan atau assigned user
        elseif ($user->hasRole('head_of_department')) {
            $jabatan = $user->profile?->jabatan ?? 'hod';
            $data = array_merge($data, [
                'totalKaryawan'      => User::where('status', 'aktif')->count(),
                'hadirBulanIni'      => Absensi::where('tanggal', now()->format('Y-m-01'))->where('status', 'hadir')->count(),
                'suratMenungguCount' => DocumentApproval::where('status', 'waiting')
                                            ->where(function($q) use ($jabatan, $user) {
                                                $q->where('jabatan', $jabatan)
                                                  ->orWhere('assigned_user_id', $user->id);
                                            })
                                            ->where('document_type', 'LIKE', 'surat_%')
                                            ->count(),
                'suratMenungguList'  => Surat::whereHas('approvals', function($q) use ($jabatan, $user) {
                                                $q->where(function($q2) use ($jabatan, $user) {
                                                    $q2->where('jabatan', $jabatan)
                                                       ->orWhere('assigned_user_id', $user->id);
                                                })->where('status', 'waiting');
                                            })
                                            ->with('user')
                                            ->orderBy('created_at', 'desc')
                                            ->take(5)
                                            ->get(),
            ]);
        }

        // ── staff / default: surat milik sendiri ────────────────────────
        // klo user staff atau role lain yg gk terdaftar, cuma tampilin data surat milik sendiri
        else {
            $suratStaff = Surat::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            $data = array_merge($data, [
                'suratStaff'             => $suratStaff->take(10),
                'suratStaffDiajukan'     => $suratStaff->where('status', 'submitted')->count(),
                'suratStaffProses'       => $suratStaff->whereIn('status', ['submitted'])->filter(function($s) {
                                                return $s->approvals()->where('status', 'approved')->count() > 0;
                                            })->count(),
                'suratStaffSelesai'      => $suratStaff->where('status', 'approved_owner')->count(),
                'suratStaffRevisiCount'  => $suratStaff->where('status', 'revised')->count(),
            ]);
        }

        return view('dashboard.home', $data);
    }

    // method untuk halaman full activity log dengan filter & paginasi
    public function activityLog(Request $request)
    {
        // Hanya HR yang bisa mengakses halaman ini
        if (!auth()->user()->hasRole('hr')) {
            abort(403);
        }

        $query = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs      = $query->paginate(20)->withQueryString();
        $users     = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $actions   = \App\Models\ActivityLog::select('action')->distinct()->pluck('action');
        $totalLogs = \App\Models\ActivityLog::count();

        return view('dashboard.activity-log', compact('logs', 'users', 'actions', 'totalLogs'));
    }

    // helper private: hitung total jam lembur karyawan dalam 30 hari terakhir, dgn asumsi kerja 8 jam/hari, kelebihan dihitung sbg lembur
    private function getTotalJamLembur(): float
    {
        $tanggalAwal  = now()->subDays(30)->format('Y-m-d');
        $totalLembur  = 0;

        // loop tiap record absensi 30 hari terakhir yg punya jam_masuk & jam_keluar, hitung selisih jam, klo >8 jam tambahin ke total lembur
        Absensi::whereBetween('tanggal', [$tanggalAwal, now()->format('Y-m-d')])
            ->whereNotNull('jam_keluar')
            ->whereNotNull('jam_masuk')
            ->get()
            ->each(function ($a) use (&$totalLembur) {
                $selisih = (strtotime($a->jam_keluar) - strtotime($a->jam_masuk)) / 3600;
                if ($selisih > 8) $totalLembur += $selisih - 8;
            });

        return $totalLembur;
    }

    // helper private: generate data chart absensi 7 hari terakhir, return labels & 4 dataset (hadir/izin/sakit/alpha) dgn warna masing2
    private function getChartAbsensi(): array
    {
        $labels = [];
        $hadir = $izin = $sakit = $alpha = [];

        // loop 7 hari terakhir, ambil tgl & format label, lalu hitung count absensi per status buat tiap hari
        for ($i = 6; $i >= 0; $i--) {
            $tgl      = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D, d M');

            $hadir[]  = Absensi::where('tanggal', $tgl)->where('status', 'hadir')->count();
            $izin[]   = Absensi::where('tanggal', $tgl)->where('status', 'izin')->count();
            $sakit[]  = Absensi::where('tanggal', $tgl)->where('status', 'sakit')->count();
            $alpha[]  = Absensi::where('tanggal', $tgl)->where('status', 'alpha')->count();
        }

        // return array dgn labels & 4 dataset yg udah disiapin dgn warna border & background masing2 utk chartjs
        return [
            'labels'   => $labels,
            'datasets' => [
                ['label' => 'hadir',  'data' => $hadir,  'borderColor' => '#10b981', 'backgroundColor' => 'rgba(16,185,129,.1)'],
                ['label' => 'izin',   'data' => $izin,   'borderColor' => '#f59e0b', 'backgroundColor' => 'rgba(245,158,11,.1)'],
                ['label' => 'sakit',  'data' => $sakit,  'borderColor' => '#3b82f6', 'backgroundColor' => 'rgba(59,130,246,.1)'],
                ['label' => 'alpha',  'data' => $alpha,  'borderColor' => '#ef4444', 'backgroundColor' => 'rgba(239,68,68,.1)'],
            ],
        ];
    }
}