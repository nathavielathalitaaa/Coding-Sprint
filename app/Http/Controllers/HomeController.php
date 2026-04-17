<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Penggajian;
use App\Models\Surat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // statistik utama dashboard
        $totalKaryawan = User::where('status', 'aktif')
            ->where('role_name', 'karyawan')
            ->count();

        $hadirHariIni = Absensi::where('tanggal', now()->format('Y-m-d'))
            ->where('status', 'hadir')
            ->count();

        $cutiMenungguCount = Leave::where('status', 'menunggu')->count();

        $totalDepartemen = Department::count();

        // total gaji dibayar bulan ini
        $bulanSekarang = now()->format('Y-m');
        $totalGajiBayar = Penggajian::where('periode', $bulanSekarang)
            ->where('status', 'dibayar')
            ->sum('gaji_bersih');

        // total jam lembur 30 hari terakhir
        $tanggalAwal = now()->subDays(30)->format('Y-m-d');
        $totalJamLembur = 0;

        $absensis30Hari = Absensi::whereBetween('tanggal', [$tanggalAwal, now()->format('Y-m-d')])
            ->whereNotNull('jam_keluar')
            ->whereNotNull('jam_masuk')
            ->get();

        foreach ($absensis30Hari as $absensi) {
            $jamKeluar = strtotime($absensi->jam_keluar);
            $jamMasuk = strtotime($absensi->jam_masuk);
            $selisih = ($jamKeluar - $jamMasuk) / 3600; // ubah ke jam
            
            if ($selisih > 8) {
                $totalJamLembur += $selisih - 8;
            }
        }

        // data chart absensi 7 hari terakhir
        $chartAbsensi = $this->getChartAbsensi();

        // data chart jam lembur 7 hari terakhir
        $chartJamLembur = $this->getChartJamLembur();

        // 5 cuti menunggu terbaru
        $cutiMenungguTerbaru = Leave::where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // karyawan yang lembur hari ini
        $karyawanLemburHariIni = $this->getKaryawanLemburHariIni();

        // Data untuk supervisor: surat menunggu approval
        $suratMenungguApproval = Surat::where('status', 'submitted')->count();

        // Data untuk staff: surat milik user saat ini
        $suratStaff = Surat::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Pre-computed stats untuk staff
        $suratStaffPendingCount = $suratStaff->whereIn('status', ['submitted', 'approved_supervisor'])->count();
        $suratStaffRevisiCount = $suratStaff->where('status', 'revised')->count();

        return view('dashboard.home', compact(
            'totalKaryawan',
            'hadirHariIni',
            'cutiMenungguCount',
            'totalDepartemen',
            'totalGajiBayar',
            'totalJamLembur',
            'chartAbsensi',
            'chartJamLembur',
            'cutiMenungguTerbaru',
            'karyawanLemburHariIni',
            'suratMenungguApproval',
            'suratStaff',
            'suratStaffPendingCount',
            'suratStaffRevisiCount',
        ));
    }

    /**
     * ambil data chart absensi 7 hari terakhir untuk chart.js
     */
    private function getChartAbsensi(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labels[] = $tanggal->format('D, d M');

            $hadir = Absensi::where('tanggal', $tanggal->format('Y-m-d'))
                ->where('status', 'hadir')
                ->count();

            $izin = Absensi::where('tanggal', $tanggal->format('Y-m-d'))
                ->where('status', 'izin')
                ->count();

            $sakit = Absensi::where('tanggal', $tanggal->format('Y-m-d'))
                ->where('status', 'sakit')
                ->count();

            $alpha = Absensi::where('tanggal', $tanggal->format('Y-m-d'))
                ->where('status', 'alpha')
                ->count();

            $data[] = [
                'tanggal' => $tanggal->format('Y-m-d'),
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'hadir',
                    'data' => array_column($data, 'hadir'),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'izin',
                    'data' => array_column($data, 'izin'),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
                [
                    'label' => 'sakit',
                    'data' => array_column($data, 'sakit'),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'alpha',
                    'data' => array_column($data, 'alpha'),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
        ];
    }

    /**
     * ambil data chart jam lembur 7 hari terakhir untuk chart.js
     */
    private function getChartJamLembur(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labels[] = $tanggal->format('D, d M');

            $jamLembur = 0;
            $absensisHari = Absensi::where('tanggal', $tanggal->format('Y-m-d'))
                ->whereNotNull('jam_keluar')
                ->whereNotNull('jam_masuk')
                ->get();

            foreach ($absensisHari as $absensi) {
                $jamKeluar = strtotime($absensi->jam_keluar);
                $jamMasuk = strtotime($absensi->jam_masuk);
                $selisih = ($jamKeluar - $jamMasuk) / 3600;
                
                if ($selisih > 8) {
                    $jamLembur += $selisih - 8;
                }
            }

            $data[] = round($jamLembur, 2);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'jam lembur',
                    'data' => $data,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
        ];
    }

    /**
     * ambil daftar karyawan yang lembur hari ini
     */
    private function getKaryawanLemburHariIni(): array
    {
        $karyawan = [];
        $absensisHariIni = Absensi::where('tanggal', now()->format('Y-m-d'))
            ->whereNotNull('jam_keluar')
            ->whereNotNull('jam_masuk')
            ->with('user')
            ->get();

        foreach ($absensisHariIni as $absensi) {
            $jamKeluar = strtotime($absensi->jam_keluar);
            $jamMasuk = strtotime($absensi->jam_masuk);
            $jamKerja = ($jamKeluar - $jamMasuk) / 3600;

            if ($jamKerja > 8) {
                $karyawan[] = [
                    'nama' => $absensi->user->name,
                    'email' => $absensi->user->email,
                    'departemen' => $absensi->user->department,
                    'jam_kerja' => round($jamKerja, 2),
                    'jam_lembur' => round($jamKerja - 8, 2),
                    'jam_masuk' => $absensi->jam_masuk,
                    'jam_keluar' => $absensi->jam_keluar,
                ];
            }
        }

        return $karyawan;
    }
}
