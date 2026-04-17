<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // tampilkan daftar absensi berdasarkan bulan
    public function index(Request $request)
    {
        // ambil bulan yang dipilih, default bulan ini
        $bulan = $request->bulan ?? date('Y-m');
        
        // ambil semua absensi di bulan tersebut beserta data usernya
        $absensiList = Absensi::with('user')
            ->where('tanggal', 'like', $bulan . '%')
            ->orderBy('tanggal', 'desc')
            ->get();
        
        return view('HR.absensi.index', compact('absensiList', 'bulan'));
    }

    // simpan data absensi yang diinput hr
    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'tanggal'   => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'status'    => 'required|in:hadir,izin,sakit,alpha,cuti',
        ], [
            // pesan validasi dalam bahasa indonesia
            'user_id.required'   => 'karyawan harus dipilih',
            'tanggal.required'   => 'tanggal harus diisi',
            'status.required'    => 'status kehadiran harus dipilih',
            'status.in'          => 'status tidak valid',
        ]);

        try {
            // cek apakah absensi tanggal ini sudah ada
            $sudahAda = Absensi::where('user_id', $request->user_id)
                ->where('tanggal', $request->tanggal)
                ->first();

            if ($sudahAda) {
                flash()->error('absensi untuk karyawan ini pada tanggal tersebut sudah ada');
                return redirect()->back();
            }

            // simpan data absensi baru
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

    // karyawan melakukan absen masuk
    public function clockIn(Request $request)
    {
        try {
            $userId  = auth()->id();
            $hari_ini = date('Y-m-d');
            $jam_ini  = date('H:i:s');

            // cek apakah sudah absen hari ini
            $sudahAbsen = Absensi::where('user_id', $userId)
                ->where('tanggal', $hari_ini)
                ->first();

            if ($sudahAbsen) {
                flash()->error('kamu sudah melakukan absen masuk hari ini');
                return redirect()->back();
            }

            // buat record absensi baru
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

    // karyawan melakukan absen keluar
    public function clockOut(Request $request)
    {
        try {
            $userId  = auth()->id();
            $hari_ini = date('Y-m-d');
            $jam_ini  = date('H:i:s');

            // cari record absensi hari ini
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

            // update jam keluar
            $absensi->update(['jam_keluar' => $jam_ini]);

            flash()->success('berhasil absen keluar pukul ' . $jam_ini);
            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal melakukan absen keluar');
            return redirect()->back();
        }
    }
}
