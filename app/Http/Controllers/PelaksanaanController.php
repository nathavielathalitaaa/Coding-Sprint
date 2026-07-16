<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\ProgressUpdate;
use App\Models\LaporanPertanggungjawaban;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelaksanaanController extends Controller
{
    public function __construct(
        private NotificationService $notifService
    ) {
        $this->middleware('auth');
    }

    /**
     * Tampilkan daftar pelaksanaan kegiatan milik user login.
     */
    public function index()
    {
        $user = Auth::user();

        $surats = Surat::where('pic_user_id', $user->id)
            ->where('status_pelaksanaan', '!=', 'selesai')
            ->with(['kegiatanDetail', 'organisasi', 'progressUpdates' => function ($q) {
                $q->latest();
            }])
            ->latest()
            ->get();

        return view('pelaksanaan.monitoring', compact('surats'));
    }

    /**
     * Simpan update progress.
     */
    public function updateProgress(Request $request, Surat $surat)
    {
        if ((int)$surat->pic_user_id !== (int)Auth::id()) {
            abort(403, 'Anda bukan PIC untuk kegiatan ini.');
        }

        $request->validate([
            'persentase' => 'required|integer|min:0|max:100',
            'catatan'    => 'required|string|max:1000',
        ]);

        ProgressUpdate::create([
            'surat_id'   => $surat->id,
            'user_id'    => Auth::id(),
            'persentase' => $request->persentase,
            'catatan'    => $request->catatan,
        ]);

        $updates = [];
        if ($surat->status_pelaksanaan === 'belum_mulai') {
            $updates['status_pelaksanaan'] = 'berjalan';
        }
        
        if (!empty($updates)) {
            $surat->update($updates);
        }

        flash()->success('Berhasil memperbarui progress pelaksanaan.');
        return redirect()->back();
    }

    /**
     * Tandai pelaksanaan selesai dan generate draft LPJ.
     */
    public function selesai(Request $request, Surat $surat)
    {
        if ((int)$surat->pic_user_id !== (int)Auth::id()) {
            abort(403, 'Anda bukan PIC untuk kegiatan ini.');
        }

        $request->validate([
            'catatan_penutup' => 'required|string|max:1000',
        ]);

        // Simpan progress terakhir (100%)
        ProgressUpdate::create([
            'surat_id'   => $surat->id,
            'user_id'    => Auth::id(),
            'persentase' => 100,
            'catatan'    => 'Kegiatan Selesai: ' . $request->catatan_penutup,
        ]);

        // Update status pelaksanaan surat
        $surat->update([
            'status_pelaksanaan' => 'selesai',
        ]);

        // Auto-create draft LPJ
        LaporanPertanggungjawaban::updateOrCreate(
            ['surat_id' => $surat->id],
            [
                'ringkasan_kegiatan' => $request->catatan_penutup,
                'realisasi_anggaran' => [],
                'status'             => 'draft',
            ]
        );

        // Kirim notifikasi ke PIC
        $kegiatanNama = $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal;
        $this->notifService->send(
            $surat->pic_user_id,
            'LPJ Perlu Diisi',
            "Pelaksanaan kegiatan '{$kegiatanNama}' telah ditandai selesai. Silakan isi Laporan Pertanggungjawaban (LPJ) Anda.",
            route('lpj.create', $surat->id)
        );

        flash()->success('Kegiatan berhasil ditandai selesai. Draft LPJ telah dibuat.');
        return redirect()->route('pelaksanaan.index');
    }
}
