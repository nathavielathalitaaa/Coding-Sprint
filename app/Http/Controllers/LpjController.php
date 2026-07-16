<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\LaporanPertanggungjawaban;
use App\Models\LpjLampiran;
use App\Services\NotificationService;
use App\Services\PinVerificationService;
use App\Jobs\GenerateLpjEmbedding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LpjController extends Controller
{
    public function __construct(
        private NotificationService $notifService,
        private PinVerificationService $pinService
    ) {
        $this->middleware('auth');
    }

    /**
     * Tampilkan formulir pengisian/edit LPJ.
     */
    public function create(Surat $surat)
    {
        if ((int)$surat->pic_user_id !== (int)Auth::id()) {
            abort(403, 'Anda bukan PIC untuk kegiatan ini.');
        }

        $lpj = $surat->lpj;
        if (!$lpj) {
            flash()->error('Draft LPJ belum dibuat. Selesaikan kegiatan terlebih dahulu.');
            return redirect()->route('pelaksanaan.index');
        }

        if (!in_array($lpj->status, ['draft', 'revisi'])) {
            flash()->error('LPJ sudah diajukan atau telah disetujui.');
            return redirect()->route('pelaksanaan.index');
        }

        return view('lpj.create', compact('surat', 'lpj'));
    }

    /**
     * Mengarahkan route edit ke halaman pengisian LPJ.
     */
    public function edit(Surat $surat)
    {
        return $this->create($surat);
    }

    /**
     * Simpan & ajukan LPJ.
     */
    public function store(Request $request, Surat $surat)
    {
        if ((int)$surat->pic_user_id !== (int)Auth::id()) {
            abort(403, 'Anda bukan PIC untuk kegiatan ini.');
        }

        $lpj = $surat->lpj;
        if (!$lpj || !in_array($lpj->status, ['draft', 'revisi'])) {
            abort(400, 'LPJ tidak dalam status draft atau revisi.');
        }

        $request->validate([
            'ringkasan_kegiatan' => 'required|string',
            'realisasi_anggaran' => 'nullable|array',
            'realisasi_anggaran.*.item'   => 'required|string',
            'realisasi_anggaran.*.jumlah' => 'required|numeric|min:0',
            'lampirans' => 'nullable|array',
            'lampirans.*.file' => 'required|file|max:10240',
            'lampirans.*.tipe' => 'required|in:foto,video,kwitansi,lainnya',
            'lampirans.*.keterangan' => 'nullable|string|max:255',
        ]);

        // Clean and prepare realisasi anggaran
        $anggaran = [];
        if ($request->has('realisasi_anggaran')) {
            foreach ($request->realisasi_anggaran as $row) {
                if (!empty($row['item'])) {
                    $anggaran[] = [
                        'item'   => $row['item'],
                        'jumlah' => (float)$row['jumlah'],
                    ];
                }
            }
        }

        $lpj->update([
            'ringkasan_kegiatan' => $request->ringkasan_kegiatan,
            'realisasi_anggaran' => $anggaran,
            'status'             => 'submitted',
        ]);

        // Upload lampiran
        if ($request->has('lampirans')) {
            foreach ($request->input('lampirans') as $index => $data) {
                $file = $request->file("lampirans.{$index}.file");
                if ($file) {
                    $path = $file->store('lpj_lampirans', 'public');
                    $lpj->lpjLampirans()->create([
                        'file_path'  => $path,
                        'tipe'       => $data['tipe'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
                }
            }
        }

        $kegiatanNama = $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal;

        $this->notifService->sendToJabatan(
            'pembina',
            'LPJ Perlu Diverifikasi',
            "LPJ untuk kegiatan '{$kegiatanNama}' telah diajukan oleh PIC dan membutuhkan verifikasi.",
            route('lpj.verifikasi.index')
        );

        flash()->success('Laporan Pertanggungjawaban (LPJ) berhasil diajukan.');
        return redirect()->route('pelaksanaan.index');
    }

    /**
     * Tampilkan detail LPJ.
     */
    public function show(Surat $surat)
    {
        $lpj = $surat->lpj()->with('lpjLampirans')->firstOrFail();
        return view('lpj.show', compact('surat', 'lpj'));
    }

    /**
     * Halaman daftar verifikasi LPJ untuk Pembina/Admin.
     */
    public function indexVerifikasi()
    {
        $user = Auth::user();

        // Get submitted LPJs
        $query = LaporanPertanggungjawaban::where('status', 'submitted')
            ->with(['surat.kegiatanDetail', 'surat.organisasi', 'lpjLampirans']);

        // Filter: Admin sees all, Pembina only sees their organization's LPJs
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $orgIds = \App\Models\OrganisasiMember::where('user_id', $user->id)
                ->where('jabatan', 'pembina')
                ->pluck('organisasi_id');

            $query->whereHas('surat', function ($q) use ($orgIds) {
                $q->whereIn('organisasi_id', $orgIds);
            });
        }

        $lpjs = $query->latest()->get();

        return view('lpj.verifikasi', compact('lpjs'));
    }

    /**
     * Aksi verifikasi LPJ (Revisi atau Valid dengan TTD).
     */
    public function verify(Request $request, Surat $surat)
    {
        $lpj = $surat->lpj;
        if (!$lpj || $lpj->status !== 'submitted') {
            abort(404, 'LPJ tidak ditemukan atau tidak dalam status verifikasi.');
        }

        $user = Auth::user();

        // Security check
        $isAuthorized = $user->hasRole('admin') || $user->hasRole('super-admin') ||
            ($surat->organisasi && \App\Models\OrganisasiMember::where('organisasi_id', $surat->organisasi_id)
                ->where('user_id', $user->id)
                ->where('jabatan', 'pembina')
                ->exists());

        if (!$isAuthorized) {
            abort(403, 'Anda tidak berwenang memverifikasi LPJ ini.');
        }

        $request->validate([
            'action' => 'required|in:reject,approve',
            'catatan_revisi' => 'required_if:action,reject|nullable|string|max:1000',
            'pin' => 'required_if:action,approve|nullable|string',
        ]);

        $kegiatanNama = $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal;

        if ($request->action === 'reject') {
            $lpj->update([
                'status' => 'revisi',
                'catatan_revisi' => $request->catatan_revisi,
            ]);

            // Notify PIC
            $this->notifService->send(
                $surat->pic_user_id,
                'LPJ Perlu Revisi',
                "LPJ kegiatan '{$kegiatanNama}' dikembalikan untuk direvisi: {$request->catatan_revisi}.",
                route('lpj.create', $surat->id)
            );

            flash()->warning('LPJ dikembalikan untuk revisi.');
            return redirect()->route('lpj.verifikasi.index');
        }

        // Approve action (requires PIN & Signature snapshot)
        if (!$this->pinService->verify($user, $request->pin)) {
            flash()->error('PIN salah. Silakan coba lagi.');
            return redirect()->back();
        }

        $ttdSnapshot = $this->pinService->getTtdPath($user);
        if (!$ttdSnapshot) {
            flash()->error('Tanda tangan digital belum diunggah. Lengkapi profil Anda terlebih dahulu.');
            return redirect()->back();
        }

        // Generate keywords for archive search
        $orgNama = $surat->organisasi->nama ?? '';
        $jenisSurat = $surat->suratType->nama ?? '';
        $keywords = implode(' ', array_filter([
            $kegiatanNama,
            $lpj->ringkasan_kegiatan,
            $orgNama,
            $jenisSurat,
        ]));

                $lpj->update([
            'status'      => 'valid',
            'verified_by' => $user->id,
            'verified_at' => now(),
            'ttd_path'    => $ttdSnapshot,
            'keywords'    => $keywords,
            'archived_at' => now(),
        ]);
        GenerateLpjEmbedding::dispatch($lpj->id);

        // Notify PIC of approval/archiving
        $this->notifService->send(
            $surat->pic_user_id,
            'LPJ Disahkan & Diarsipkan',
            "LPJ untuk kegiatan '{$kegiatanNama}' telah disahkan oleh Pembina/Admin dan resmi diarsipkan.",
            route('lpj.show', $surat->id)
        );

        flash()->success('LPJ berhasil disetujui, ditandatangani, dan diarsipkan.');
        return redirect()->route('lpj.verifikasi.index');
    }
}
