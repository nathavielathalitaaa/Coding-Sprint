<?php

namespace App\Services;

use App\Models\Organisasi;
use App\Models\OrganisasiMember;
use App\Models\Surat;
use App\Models\SuratTurunan;
use App\Models\SuratTurunanSigner;
use App\Models\SuratTurunanTemplate;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class SuratTurunanService
{
    // ══════════════════════════════════════════════════════════════════════
    // 1. RESOLUSI SIGNER
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Resolve 3 kandidat penandatangan aktual untuk surat induk.
     *
     * Slot  → Sumber resolusi
     * ────────────────────────────────────────────────────────────────────
     * ketua_pelaksana → OrganisasiMember(organisasi_id, jabatan='ketua')
     * pembina         → OrganisasiMember(organisasi_id, jabatan='pembina')
     * kepala_sekolah  → User dengan Spatie role 'kepala_sekolah'
     *
     * @return array<string, User|null>  [jabatan_slot => User|null]
     */
    public function getEligibleSigners(Surat $surat): array
    {
        $organisasiId = $surat->organisasi_id;

        // ── Ketua Pelaksana ────────────────────────────────────────────
        // Nilai jabatan 'ketua' terkonfirmasi dari OrganisasiSeeder & OrganisasiMember::jabatanOptions()
        $ketuaMember = OrganisasiMember::where('organisasi_id', $organisasiId)
            ->where('jabatan', 'ketua')
            ->with('user')
            ->first();

        // ── Pembina ────────────────────────────────────────────────────
        $pembinaMember = OrganisasiMember::where('organisasi_id', $organisasiId)
            ->where('jabatan', 'pembina')
            ->with('user')
            ->first();

        // ── Kepala Sekolah ─────────────────────────────────────────────
        // Reuse pola checkGlobalRole() dari ApprovalService: role Spatie 'kepala_sekolah'
        $kepalaSekolah = User::role('kepala_sekolah')->first();

        return [
            SuratTurunanSigner::SLOT_KETUA_PELAKSANA => $ketuaMember?->user,
            SuratTurunanSigner::SLOT_PEMBINA         => $pembinaMember?->user,
            SuratTurunanSigner::SLOT_KEPALA_SEKOLAH  => $kepalaSekolah,
        ];
    }

    // ══════════════════════════════════════════════════════════════════════
    // 2. GENERATE SURAT TURUNAN
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Generate satu instance surat turunan dari template + data surat induk.
     *
     * @param  Surat   $surat              Surat induk (harus approved_owner)
     * @param  string  $kodeTemplate       Kode template: 'undangan', 'izin_kegiatan', dst.
     * @param  string[] $jabatanSlotSigners Slot yang dipilih sekretaris, cth: ['ketua_pelaksana', 'kepala_sekolah']
     * @param  int     $createdBy          user_id sekretaris
     * @return SuratTurunan
     *
     * @throws \InvalidArgumentException  Jika template tidak ditemukan atau tidak aktif
     * @throws \RuntimeException          Jika surat induk belum punya detail kegiatan
     */
    public function generate(
        Surat $surat,
        string $kodeTemplate,
        array $jabatanSlotSigners,
        int $createdBy
    ): SuratTurunan {
        // ── Guard: template harus ada & aktif ──────────────────────────
        $template = SuratTurunanTemplate::where('kode', $kodeTemplate)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            throw new \InvalidArgumentException(
                "Template surat turunan '{$kodeTemplate}' tidak ditemukan atau tidak aktif."
            );
        }

        // ── Guard: detail kegiatan harus ada ──────────────────────────
        $detail = $surat->kegiatanDetail;
        if (!$detail) {
            throw new \RuntimeException(
                "Surat #{$surat->id} tidak memiliki detail kegiatan. Pastikan data kegiatan sudah diisi."
            );
        }

        // ── Load relasi yang diperlukan untuk rendering ────────────────
        $surat->load('organisasi');

        // ── Render konten (replace semua placeholder) ──────────────────
        $kontenFinal = $this->renderTemplate($template->konten_template, $surat, $detail);

        // ── Tentukan status awal: langsung menunggu_ttd jika ada signer ─
        $statusAwal = count($jabatanSlotSigners) > 0
            ? SuratTurunan::STATUS_MENUNGGU_TTD
            : SuratTurunan::STATUS_DRAFT;

        // ── Buat nomor surat turunan (format: KODE/ID/TAHUN) ──────────
        $nomorSurat = strtoupper($kodeTemplate) . '/' . $surat->id . '/' . now()->year;

        // ── Resolusi user aktual per slot ──────────────────────────────
        $eligibleSigners = $this->getEligibleSigners($surat);

        return DB::transaction(function () use (
            $surat, $template, $kontenFinal, $nomorSurat,
            $statusAwal, $jabatanSlotSigners, $eligibleSigners, $createdBy
        ) {
            // Buat baris SuratTurunan
            $suratTurunan = SuratTurunan::create([
                'surat_id'                  => $surat->id,
                'surat_turunan_template_id' => $template->id,
                'nomor_surat'               => $nomorSurat,
                'konten_final'              => $kontenFinal,
                'file_pdf_path'             => null,
                'status'                    => $statusAwal,
                'created_by'               => $createdBy,
            ]);

            // Buat baris SuratTurunanSigner untuk setiap slot yang dipilih
            foreach ($jabatanSlotSigners as $slot) {
                $resolvedUser = $eligibleSigners[$slot] ?? null;

                SuratTurunanSigner::create([
                    'surat_turunan_id' => $suratTurunan->id,
                    'user_id'          => $resolvedUser?->id,
                    'jabatan_slot'     => $slot,
                    'status'           => SuratTurunanSigner::STATUS_WAITING,
                    'ttd_snapshot'     => null,
                    'signed_at'        => null,
                ]);
            }

            return $suratTurunan;
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    // 3. TANDA TANGAN
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Catat TTD seorang signer pada surat turunan.
     * Jika semua signer sudah TTD, otomatis render PDF final & update status.
     *
     * @param  SuratTurunanSigner $signer       Baris signer yang akan di-sign
     * @param  string             $ttdSnapshot  Path relatif file TTD (dari storage public)
     * @throws \RuntimeException  Jika signer sudah pernah TTD sebelumnya
     */
    public function sign(SuratTurunanSigner $signer, string $ttdSnapshot): void
    {
        // ── Guard: jangan TTD dua kali ─────────────────────────────────
        if ($signer->sudahTtd()) {
            throw new \RuntimeException(
                "Slot '{$signer->jabatan_slot}' sudah pernah menandatangani surat turunan ini."
            );
        }

        DB::transaction(function () use ($signer, $ttdSnapshot) {
            // Catat TTD signer ini
            $signer->update([
                'status'       => SuratTurunanSigner::STATUS_SIGNED,
                'ttd_snapshot' => $ttdSnapshot,
                'signed_at'    => now(),
            ]);

            // Refresh relasi agar semuaSignerSudahTtd() membaca data terbaru
            $signer->refresh();
            $suratTurunan = $signer->suratTurunan()->with('signers')->first();

            if ($suratTurunan->semuaSignerSudahTtd()) {
                // Generate PDF final
                $pdfPath = $this->generatePdfFinal($suratTurunan);

                $suratTurunan->update([
                    'status'        => SuratTurunan::STATUS_DITANDATANGANI,
                    'file_pdf_path' => $pdfPath,
                ]);

                Log::info("SuratTurunan #{$suratTurunan->id} selesai ditandatangani, PDF: {$pdfPath}");
            }
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Replace semua placeholder {{token}} di konten template dengan data asli.
     * Format tanggal: 'd F Y' (cth: 14 Juli 2026).
     */
    private function renderTemplate(
        string $konten,
        Surat $surat,
        \App\Models\SuratKegiatanDetail $detail
    ): string {
        // Format tanggal selesai: kosong jika null, atau " s.d. DD Month YYYY"
        $tanggalSelesai = $detail->tanggal_selesai
            ? ' s.d. ' . $detail->tanggal_selesai->translatedFormat('d F Y')
            : '';

        $map = [
            '{{nama_kegiatan}}'     => $detail->nama_kegiatan,
            '{{tanggal_mulai}}'     => $detail->tanggal_mulai->translatedFormat('d F Y'),
            '{{tanggal_selesai}}'   => $tanggalSelesai,
            '{{lokasi}}'            => $detail->lokasi,
            '{{deskripsi_singkat}}' => $detail->deskripsi_singkat ?? '-',
            '{{organisasi_nama}}'   => $surat->organisasi?->nama ?? '-',
            '{{nomor_surat_induk}}' => $surat->nomor_surat ?? '-',
            '{{tanggal_surat}}'     => now()->translatedFormat('d F Y'),
        ];

        return str_replace(array_keys($map), array_values($map), $konten);
    }

    /**
     * Render PDF final dari konten_final + blok tanda tangan semua signer.
     * Menggunakan FPDI (sudah tersedia via barryvdh/laravel-dompdf tidak cukup untuk
     * manipulasi raw FPDI; kita pakai FPDI langsung seperti PdfStampService).
     *
     * Hasil disimpan ke storage/app/public/surat-turunan/{id}_{kode}_{timestamp}.pdf
     *
     * @return string  Path relatif (dari storage/app/public)
     */
    private function generatePdfFinal(SuratTurunan $suratTurunan): string
    {
        // Load signer dengan user untuk nama & TTD
        $suratTurunan->load('signers.user', 'template');

        $pdf = new Fpdi();
        $pdf->SetMargins(20, 20, 20);
        $pdf->AddPage('P', 'A4');

        $pageWidth  = $pdf->GetPageWidth();   // 210mm
        $contentWidth = $pageWidth - 40;       // margin kiri+kanan = 40mm

        // ── Header: Nomor Surat ────────────────────────────────────────
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell($contentWidth, 8, $suratTurunan->template?->nama ?? 'SURAT TURUNAN', 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell($contentWidth, 6, 'Nomor: ' . ($suratTurunan->nomor_surat ?? '-'), 0, 1, 'C');
        $pdf->Ln(4);

        // ── Konten Utama ───────────────────────────────────────────────
        // FPDI/FPDF tidak support rich text; tampilkan sebagai MultiCell baris per baris
        $pdf->SetFont('Helvetica', '', 10);
        $lines = explode("\n", $suratTurunan->konten_final);
        foreach ($lines as $line) {
            // Deteksi baris separator TTD (garis bawah/tanda tangan mock)
            if (str_contains($line, '_______')) {
                // Skip baris TTD mock dari template; akan digantikan blok resmi di bawah
                continue;
            }
            // Render sebagai MultiCell agar wrap otomatis
            $pdf->MultiCell($contentWidth, 5, $line, 0, 'L');
        }

        $pdf->Ln(8);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(10);

        // ── Blok Tanda Tangan ──────────────────────────────────────────
        $signers      = $suratTurunan->signers->where('status', SuratTurunanSigner::STATUS_SIGNED);
        $signerCount  = $signers->count();

        if ($signerCount > 0) {
            $colWidth  = $contentWidth / $signerCount;
            $startY    = $pdf->GetY();

            foreach ($signers->values() as $idx => $signer) {
                $xPos = 20 + ($idx * $colWidth);

                // Label jabatan
                $pdf->SetXY($xPos, $startY);
                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->Cell($colWidth, 5, strtoupper($signer->jabatanLabel), 0, 0, 'C');

                // Gambar TTD (jika ada)
                $ttdPath = $this->resolveTtdPath($signer->ttd_snapshot, $signer->user);
                if ($ttdPath) {
                    try {
                        $imgX = $xPos + ($colWidth / 2) - 15; // center 30mm
                        $pdf->Image($ttdPath, $imgX, $startY + 8, 30, 15);
                    } catch (\Exception $e) {
                        Log::warning("Gagal embed TTD untuk slot {$signer->jabatan_slot}: " . $e->getMessage());
                    }
                }

                // Garis tanda tangan
                $lineY = $startY + 27;
                $pdf->Line($xPos + 4, $lineY, $xPos + $colWidth - 4, $lineY);

                // Nama user
                $pdf->SetXY($xPos, $lineY + 2);
                $pdf->SetFont('Helvetica', '', 9);
                $pdf->Cell($colWidth, 5, $signer->user?->name ?? '-', 0, 0, 'C');

                // Tanggal TTD
                $pdf->SetXY($xPos, $lineY + 8);
                $pdf->SetFont('Helvetica', 'I', 8);
                $tanggalTtd = $signer->signed_at
                    ? Carbon::parse($signer->signed_at)->translatedFormat('d F Y')
                    : '-';
                $pdf->Cell($colWidth, 4, $tanggalTtd, 0, 0, 'C');
            }

            $pdf->Ln(45);
        }

        // ── Footer watermark ───────────────────────────────────────────
        $pdf->SetFont('Helvetica', 'I', 7);
        $pdf->SetTextColor(150, 150, 150);
        $pageHeight = $pdf->GetPageHeight();
        $footerText = 'Dokumen ini diterbitkan secara digital oleh SIMORA · ' . now()->format('d/m/Y H:i');
        $textWidth  = $pdf->GetStringWidth($footerText);
        $pdf->SetXY($pageWidth - $textWidth - 10, $pageHeight - 8);
        $pdf->Cell($textWidth, 4, $footerText, 0, 0, 'R');

        // ── Simpan ke storage ──────────────────────────────────────────
        $dir      = storage_path('app/public/surat-turunan');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $suratTurunan->id . '_' . ($suratTurunan->template?->kode ?? 'turunan') . '_' . time() . '.pdf';
        $fullPath = $dir . '/' . $filename;

        $pdf->Output('F', $fullPath);

        return 'surat-turunan/' . $filename;
    }

    /**
     * Resolve path fisik file TTD dari snapshot path atau ttd_path user.
     * Mengikuti pola getSignaturePath() di PdfStampService.
     *
     * @param  string|null $ttdSnapshot  Path yang disimpan saat TTD
     * @param  User|null   $user         User penandatangan
     * @return string|null               Absolute path ke file gambar, atau null
     */
    private function resolveTtdPath(?string $ttdSnapshot, ?User $user): ?string
    {
        // Prioritas 1: ttd_path dari user (tanda tangan terdaftar)
        if ($user?->ttd_path) {
            $candidates = [
                storage_path('app/public/signatures/' . $user->ttd_path),
                storage_path('app/public/' . $user->ttd_path),
                storage_path('app/private/' . $user->ttd_path),
            ];
            foreach ($candidates as $path) {
                if (file_exists($path) && !is_dir($path)) {
                    return $path;
                }
            }
        }

        // Prioritas 2: snapshot yang disimpan saat TTD
        if ($ttdSnapshot) {
            $candidates = [
                storage_path('app/public/' . $ttdSnapshot),
                storage_path('app/private/' . $ttdSnapshot),
                storage_path('app/private/private/' . $ttdSnapshot),
            ];
            foreach ($candidates as $path) {
                if (file_exists($path) && !is_dir($path)) {
                    return $path;
                }
            }
        }

        return null;
    }
}
