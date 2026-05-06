<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');

        $query = Absensi::select(['id', 'user_id', 'tanggal', 'jam_masuk', 'jam_keluar', 'status', 'keterangan'])
            ->with(['user' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where('tanggal', 'like', $bulan . '%');

        // jika user bukan hr, tampilkan hanya absensi miliknya sendiri
        if (!auth()->user()->hasRole('hr')) {
            $query->where('user_id', auth()->user()->id);
        }

        $absensiList = $query->orderBy('tanggal', 'desc')->get();

        $absensiHariIni = Absensi::where('user_id', auth()->user()->id)
            ->where('tanggal', date('Y-m-d'))
            ->first();

        return view('hr.absensi.index', compact('absensiList', 'bulan', 'absensiHariIni'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('hr')) {
            abort(403, 'Akses ditolak. Hanya HR yang dapat menambah absensi manual.');
        }

        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'tanggal'   => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'status'    => 'required|in:hadir,izin,sakit,alpha,cuti',
        ], [
            'user_id.required'   => 'karyawan harus dipilih',
            'tanggal.required'   => 'tanggal harus diisi',
            'status.required'    => 'status kehadiran harus dipilih',
            'status.in'          => 'status tidak valid',
        ]);

        try {
            $sudahAda = Absensi::where('user_id', $request->user_id)
                ->where('tanggal', $request->tanggal)
                ->first();

            if ($sudahAda) {
                flash()->error('absensi untuk karyawan ini pada tanggal tersebut sudah ada');
                return redirect()->back();
            }

            Absensi::create([
                'user_id'    => $request->user_id,
                'tanggal'    => $request->tanggal,
                'jam_masuk'  => $request->jam_masuk,
                'jam_keluar' => $request->jam_keluar,
                'status'     => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            flash()->success('data absensi berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal menyimpan data absensi');
            return redirect()->back();
        }
    }

    public function clockIn(Request $request)
    {
        try {
            $userId   = auth()->id();
            $hari_ini = date('Y-m-d');
            $jam_ini  = date('H:i:s');

            $sudahAbsen = Absensi::where('user_id', $userId)
                ->where('tanggal', $hari_ini)
                ->first();

            if ($sudahAbsen) {
                flash()->error('kamu sudah melakukan absen masuk hari ini');
                return redirect()->back();
            }

            Absensi::create([
                'user_id'   => $userId,
                'tanggal'   => $hari_ini,
                'jam_masuk' => $jam_ini,
                'status'    => 'hadir',
            ]);

            flash()->success('berhasil absen masuk pukul ' . $jam_ini);
            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal melakukan absen masuk');
            return redirect()->back();
        }
    }

    public function clockOut(Request $request)
    {
        try {
            $userId   = auth()->id();
            $hari_ini = date('Y-m-d');
            $jam_ini  = date('H:i:s');

            $absensi = Absensi::where('user_id', $userId)
                ->where('tanggal', $hari_ini)
                ->first();

            if (!$absensi) {
                flash()->error('kamu belum melakukan absen masuk hari ini');
                return redirect()->back();
            }

            if ($absensi->jam_keluar) {
                flash()->error('kamu sudah melakukan absen keluar hari ini');
                return redirect()->back();
            }

            $absensi->update(['jam_keluar' => $jam_ini]);

            flash()->success('berhasil absen keluar pukul ' . $jam_ini);
            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal melakukan absen keluar');
            return redirect()->back();
        }
    }

    public function exportExcel(Request $request)
    {
        if (!auth()->user()->hasRole('hr')) {
            abort(403, 'Akses ditolak. Hanya HR yang dapat mengekspor data absensi.');
        }

        $bulan = $request->bulan ?? date('Y-m');

        $absensiList = Absensi::with(['user' => fn($q) => $q->select('id', 'name')])
            ->where('tanggal', 'like', $bulan . '%')
            ->orderBy('tanggal')
            ->get();

        $namaFile = 'rekap-absensi-' . $bulan . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
        ];

        $callback = function () use ($absensiList) {
            $file = fopen('php://output', 'w');

            // bom untuk excel agar utf-8 terbaca benar
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // header kolom
            fputcsv($file, ['No', 'Nama Karyawan', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Keterangan']);

            foreach ($absensiList as $i => $absensi) {
                fputcsv($file, [
                    $i + 1,
                    $absensi->user?->name ?? '-',
                    $absensi->tanggal,
                    $absensi->jam_masuk ?? '-',
                    $absensi->jam_keluar ?? '-',
                    ucfirst($absensi->status),
                    $absensi->keterangan ?? '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        if (!auth()->user()->hasRole('hr')) {
            abort(403, 'Akses ditolak. Hanya HR yang dapat mengekspor data absensi.');
        }

        $bulan = $request->bulan ?? date('Y-m');

        $absensiList = Absensi::with(['user' => fn($q) => $q->select('id', 'name')])
            ->where('tanggal', 'like', $bulan . '%')
            ->orderBy('tanggal')
            ->get();

        // hitung ringkasan
        $ringkasan = [
            'hadir' => $absensiList->where('status', 'hadir')->count(),
            'izin'  => $absensiList->where('status', 'izin')->count(),
            'sakit' => $absensiList->where('status', 'sakit')->count(),
            'alpha' => $absensiList->where('status', 'alpha')->count(),
            'cuti'  => $absensiList->where('status', 'cuti')->count(),
        ];

        $pdf = Pdf::loadView('hr.absensi.export-pdf', compact('absensiList', 'bulan', 'ringkasan'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('rekap-absensi-' . $bulan . '.pdf');
    }
}