<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\JadwalShift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    // tampilkan semua data shift
    public function index()
    {
        $shiftList = Shift::all();
        return view('hr.shift.index', compact('shiftList'));
    }

    // simpan atau update data shift
    public function store(Request $request)
    {
        $request->validate([
            'nama_shift'       => 'required|string|max:100',
            'jam_masuk'        => 'required',
            'jam_keluar'       => 'required',
            'toleransi_menit'  => 'required|integer|min:0|max:60',
        ], [
            'nama_shift.required'      => 'nama shift harus diisi',
            'jam_masuk.required'       => 'jam masuk harus diisi',
            'jam_keluar.required'      => 'jam keluar harus diisi',
            'toleransi_menit.required' => 'toleransi menit harus diisi',
        ]);

        try {
            Shift::updateOrCreate(
                ['id' => $request->id_update],
                [
                    'nama_shift'      => $request->nama_shift,
                    'jam_masuk'       => $request->jam_masuk,
                    'jam_keluar'      => $request->jam_keluar,
                    'toleransi_menit' => $request->toleransi_menit,
                    'keterangan'      => $request->keterangan,
                ]
            );

            flash()->success('data shift berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal menyimpan data shift');
            return redirect()->back();
        }
    }

    // hapus data shift
    public function destroy(Request $request)
    {
        try {
            $shift = Shift::findOrFail($request->id_delete);
            $shift->delete();

            flash()->success('data shift berhasil dihapus');
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal menghapus data shift');
            return redirect()->back();
        }
    }

    // tampilkan jadwal shift semua karyawan
    public function jadwal()
    {
        // ambil semua karyawan aktif
        $karyawanList = User::where('status', 'aktif')->get();
        $shiftList    = Shift::all();
        
        // ambil jadwal minggu ini
        $mulaiMinggu  = date('Y-m-d', strtotime('monday this week'));
        $akhirMinggu  = date('Y-m-d', strtotime('sunday this week'));
        
        $jadwalList = JadwalShift::with(['user', 'shift'])
            ->whereBetween('tanggal_mulai', [$mulaiMinggu, $akhirMinggu])
            ->get();

        return view('hr.shift.jadwal', compact('karyawanList', 'shiftList', 'jadwalList', 'mulaiMinggu', 'akhirMinggu'));
    }

    // simpan penugasan shift ke karyawan
    public function simpanJadwal(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'shift_id'     => 'required|exists:shifts,id',
            'tanggal_mulai' => 'required|date',
        ], [
            'user_id.required'       => 'karyawan harus dipilih',
            'shift_id.required'      => 'shift harus dipilih',
            'tanggal_mulai.required' => 'tanggal mulai harus diisi',
        ]);

        try {
            JadwalShift::create([
                'user_id'       => $request->user_id,
                'shift_id'      => $request->shift_id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
            ]);

            flash()->success('jadwal shift berhasil disimpan');
            return redirect()->back();

        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('gagal menyimpan jadwal shift');
            return redirect()->back();
        }
    }
}
