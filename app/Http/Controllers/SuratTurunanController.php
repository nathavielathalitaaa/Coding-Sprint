<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\SuratTurunan;
use App\Models\SuratTurunanSigner;
use App\Models\SuratTurunanTemplate;
use App\Services\PinVerificationService;
use App\Services\SuratTurunanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratTurunanController extends Controller
{
    public function __construct(
        private SuratTurunanService   $turunanService,
        private PinVerificationService $pinService,
    ) {
        $this->middleware('auth');
    }

    // ══════════════════════════════════════════════════════════════════
    // GUARD HELPER — dipanggil di setiap method publik
    // ══════════════════════════════════════════════════════════════════

    /**
     * Pastikan surat induk sudah approved_owner.
     * Jika tidak, redirect ke halaman surat dengan pesan error.
     */
    private function assertApprovedOwner(Surat $surat): ?\Illuminate\Http\RedirectResponse
    {
        if ($surat->status !== 'approved_owner') {
            flash()->error('Surat turunan hanya dapat dikelola setelah surat induk disetujui penuh (approved_owner).');
            return redirect()->route('surat.show', $surat->id);
        }
        return null;
    }

    /**
     * Pastikan user punya akses ke surat induk (pakai SuratPolicy::view).
     */
    private function assertCanView(Surat $surat): ?\Illuminate\Http\RedirectResponse
    {
        if (Auth::user()->cannot('view', $surat)) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }
        return null;
    }

    // ══════════════════════════════════════════════════════════════════
    // INDEX — daftar surat turunan milik surat induk
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET surat/{surat}/turunan
     * Tampilkan semua surat turunan beserta status & signer masing-masing.
     */
    public function index(Surat $surat)
    {
        if ($redirect = $this->assertApprovedOwner($surat)) return $redirect;
        if ($redirect = $this->assertCanView($surat))       return $redirect;

        $suratTurunans = $surat->suratTurunans()
            ->with(['template', 'signers.user', 'creator'])
            ->latest()
            ->get();

        return view('surat.turunan.index', compact('surat', 'suratTurunans'));
    }

    // ══════════════════════════════════════════════════════════════════
    // CREATE — form pilih template + signer
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET surat/{surat}/turunan/create
     * Form multi-pilih:
     *   - Checkbox jenis turunan (dari SuratTurunanTemplate aktif)
     *   - Per jenis: checkbox signer (hanya yang user-nya terresolve / tidak null)
     */
    public function create(Surat $surat)
    {
        if ($redirect = $this->assertApprovedOwner($surat)) return $redirect;
        if ($redirect = $this->assertCanView($surat))       return $redirect;

        // Template aktif yang tersedia
        $templates = SuratTurunanTemplate::aktif()->orderBy('nama')->get();

        // Kandidat signer aktual (null = user tidak ditemukan → tidak ditampilkan di form)
        $eligibleSigners = $this->turunanService->getEligibleSigners($surat);

        // Filter: hanya tampilkan slot yang user-nya ada
        $availableSigners = array_filter($eligibleSigners, fn($user) => $user !== null);

        return view('surat.turunan.create', compact('surat', 'templates', 'availableSigners'));
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE — generate satu atau lebih surat turunan sekaligus
    // ══════════════════════════════════════════════════════════════════

    /**
     * POST surat/{surat}/turunan
     *
     * Request:
     *   templates[]          — array kode template yang dicentang
     *   signers[{kode}][]    — array jabatan_slot yang dipilih per template
     *                          cth: signers[undangan][] = ketua_pelaksana
     *                               signers[undangan][] = kepala_sekolah
     */
    public function store(Request $request, Surat $surat)
    {
        if ($redirect = $this->assertApprovedOwner($surat)) return $redirect;
        if ($redirect = $this->assertCanView($surat))       return $redirect;

        // ── Validasi ───────────────────────────────────────────────────
        $request->validate([
            'templates'   => 'required|array|min:1',
            'templates.*' => 'required|string|exists:surat_turunan_templates,kode',
            'signers'     => 'nullable|array',
            'signers.*'   => 'nullable|array',
            'signers.*.*' => [
                'string',
                'in:' . implode(',', [
                    SuratTurunanSigner::SLOT_KETUA_PELAKSANA,
                    SuratTurunanSigner::SLOT_PEMBINA,
                    SuratTurunanSigner::SLOT_KEPALA_SEKOLAH,
                ]),
            ],
        ], [
            'templates.required' => 'Pilih minimal satu jenis surat turunan.',
            'templates.min'      => 'Pilih minimal satu jenis surat turunan.',
        ]);

        $generated = [];
        $errors    = [];

        foreach ($request->templates as $kode) {
            $jabatanSlots = $request->input("signers.{$kode}", []);

            try {
                $turunan    = $this->turunanService->generate(
                    $surat,
                    $kode,
                    $jabatanSlots,
                    Auth::id()
                );
                $generated[] = $turunan->template->nama ?? $kode;
            } catch (\Exception $e) {
                \Log::error("Gagal generate surat turunan [{$kode}]: " . $e->getMessage());
                $errors[] = "Gagal generate '{$kode}': " . $e->getMessage();
            }
        }

        // ── Feedback ───────────────────────────────────────────────────
        if (!empty($generated)) {
            $namaList = implode(', ', $generated);
            flash()->success("Berhasil membuat: {$namaList}.");
        }

        foreach ($errors as $err) {
            flash()->error($err);
        }

        return redirect()->route('surat.turunan.index', $surat->id);
    }

    // ══════════════════════════════════════════════════════════════════
    // SIGN — tanda tangan oleh signer
    // ══════════════════════════════════════════════════════════════════

    /**
     * POST surat/{surat}/turunan/{suratTurunan}/signer/{signer}/sign
     *
     * Request:
     *   pin — PIN user (wajib)
     */
    public function sign(Request $request, Surat $surat, SuratTurunan $suratTurunan, SuratTurunanSigner $signer)
    {
        if ($redirect = $this->assertApprovedOwner($surat)) return $redirect;

        // ── Pastikan signer milik suratTurunan yang benar ──────────────
        if ($signer->surat_turunan_id !== $suratTurunan->id) {
            abort(404);
        }

        // ── Pastikan surat turunan milik surat induk yang benar ────────
        if ($suratTurunan->surat_id !== $surat->id) {
            abort(404);
        }

        // ── Otorisasi: hanya user yang ditugaskan di slot ini ──────────
        $user = Auth::user();
        if ((int) $signer->user_id !== (int) $user->id) {
            flash()->error('Anda tidak berwenang menandatangani slot ini.');
            return redirect()->route('surat.turunan.index', $surat->id);
        }

        // ── Validasi request ───────────────────────────────────────────
        $request->validate([
            'pin' => 'required|string',
        ], [
            'pin.required' => 'PIN wajib diisi untuk menandatangani.',
        ]);

        // ── Verifikasi PIN ─────────────────────────────────────────────
        if (!$this->pinService->verify($user, $request->pin)) {
            flash()->error('PIN salah. Silakan coba lagi.');
            return redirect()->route('surat.turunan.index', $surat->id);
        }

        // ── Ambil snapshot TTD user ────────────────────────────────────
        $ttdSnapshot = $this->pinService->getTtdPath($user);

        if (!$ttdSnapshot) {
            flash()->error('Tanda tangan digital belum diunggah. Silakan lengkapi profil Anda terlebih dahulu.');
            return redirect()->route('surat.turunan.index', $surat->id);
        }

        // ── Proses TTD ─────────────────────────────────────────────────
        try {
            $this->turunanService->sign($signer, $ttdSnapshot);
            flash()->success('Tanda tangan berhasil disimpan.');
        } catch (\RuntimeException $e) {
            flash()->error($e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Gagal proses TTD surat turunan: ' . $e->getMessage());
            flash()->error('Terjadi kesalahan sistem saat memproses tanda tangan.');
        }

        return redirect()->route('surat.turunan.index', $surat->id);
    }

    // ══════════════════════════════════════════════════════════════════
    // DOWNLOAD — unduh PDF final
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET surat/{surat}/turunan/{suratTurunan}/download
     * Hanya tersedia setelah status 'ditandatangani' (file_pdf_path terisi).
     */
    public function download(Surat $surat, SuratTurunan $suratTurunan)
    {
        if ($redirect = $this->assertApprovedOwner($surat))  return $redirect;
        if ($redirect = $this->assertCanView($surat))         return $redirect;

        // ── Pastikan surat turunan milik surat induk yang benar ────────
        if ($suratTurunan->surat_id !== $surat->id) {
            abort(404);
        }

        // ── Pastikan PDF sudah tersedia ────────────────────────────────
        if (!$suratTurunan->file_pdf_path) {
            flash()->error('PDF surat turunan belum tersedia. Tunggu hingga semua pihak menandatangani.');
            return redirect()->route('surat.turunan.index', $surat->id);
        }

        $filePath = storage_path('app/public/' . $suratTurunan->file_pdf_path);

        if (!file_exists($filePath)) {
            flash()->error('File PDF tidak ditemukan di server.');
            return redirect()->route('surat.turunan.index', $surat->id);
        }

        // ── Buat nama file yang bersih ─────────────────────────────────
        $kode     = $suratTurunan->template?->kode ?? 'turunan';
        $nomor    = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-',
                        $suratTurunan->nomor_surat ?? $suratTurunan->id);
        $filename = "{$nomor}_{$kode}.pdf";

        return response()->download($filePath, $filename);
    }
}
