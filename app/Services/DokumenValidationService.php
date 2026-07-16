<?php

namespace App\Services;

use App\Models\SuratKegiatanDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\EmbeddingService;
use App\Support\CosineSimilarity;

/**
 * DokumenValidationService
 *
 * Validasi rule-based untuk deteksi duplikat kegiatan dan konflik jadwal lokasi.
 * Murni PHP + SQL — tidak ada AI/LLM/API eksternal sama sekali.
 *
 * Status surat yang dicek (surat "aktif"):
 *   pending_admin  — masuk antrian admin, belum diproses
 *   submitted      — sedang dalam alur approval
 *   approved_owner — sudah disetujui penuh (kegiatan akan/sedang berjalan)
 *
 * Status yang TIDAK dicek (surat "tidak aktif"):
 *   revised        — dikembalikan untuk revisi, belum final
 *   rejected       — ditolak oleh approver
 *   rejected_admin — ditolak admin sebelum diproses
 */
class DokumenValidationService
{
    public function __construct(private EmbeddingService $embeddingService)
    {
    }
    /**
     * Status surat yang dianggap "aktif" dan berpotensi konflik/duplikat.
     * Dipakai di kedua method agar konsisten.
     */
    private const ACTIVE_STATUSES = ['pending_admin', 'submitted', 'approved_owner'];

    // ══════════════════════════════════════════════════════════════════════
    // 1. DETEKSI DUPLIKAT
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Cek apakah ada kegiatan serupa dari organisasi yang sama dalam window ±7 hari.
     *
     * Algoritma:
     *   1. Ambil kandidat: surat aktif dari organisasi yang sama,
     *      tanggal_mulai dalam rentang [tanggalMulai-7, tanggalMulai+7].
     *   2. Untuk setiap kandidat, hitung kemiripan nama dengan similar_text().
     *   3. Kembalikan kandidat dengan persentase tertinggi jika >= 70%.
     *
     * @param  string   $namaKegiatan  Nama kegiatan yang akan dicek
     * @param  int      $organisasiId  ID organisasi pengaju
     * @param  string   $tanggalMulai  Format Y-m-d
     * @param  int|null $excludeSuratId  ID surat yang dikecualikan (untuk keperluan edit)
     * @return array|null  ['surat_id', 'nomor_surat', 'nama_kegiatan', 'tanggal_mulai', 'percent']
     *                     atau null jika tidak ada duplikat
     */
    public function cekDuplikat(
        string $namaKegiatan,
        int    $organisasiId,
        string $tanggalMulai,
        ?int   $excludeSuratId = null
    ): ?array {
        $tglMulai   = Carbon::parse($tanggalMulai);
        $windowMulai = $tglMulai->copy()->subDays(7)->toDateString();
        $windowAkhir = $tglMulai->copy()->addDays(7)->toDateString();

        $query = SuratKegiatanDetail::query()
            ->join('surats', 'surats.id', '=', 'surat_kegiatan_details.surat_id')
            ->whereIn('surats.status', self::ACTIVE_STATUSES)
            ->where('surats.organisasi_id', $organisasiId)
            ->whereBetween('surat_kegiatan_details.tanggal_mulai', [$windowMulai, $windowAkhir])
            ->select([
                'surats.id          as surat_id',
                'surats.nomor_surat as nomor_surat',
                'surat_kegiatan_details.nama_kegiatan',
                'surat_kegiatan_details.tanggal_mulai',
            ]);

        // Kecualikan surat yang sedang diedit
        if ($excludeSuratId !== null) {
            $query->where('surats.id', '!=', $excludeSuratId);
        }

        $kandidats = $query->get();

        if ($kandidats->isEmpty()) {
            return null;
        }

        // Coba pakai AI semantic search dulu
        $inputVector = $this->embeddingService->embed($namaKegiatan);
        
        $tertinggi = null;

        if ($inputVector !== null) {
            // Berhasil kontak service AI -> gunakan Cosine Similarity
            foreach ($kandidats as $kandidat) {
                $kandidatVector = $this->embeddingService->embed($kandidat->nama_kegiatan);
                
                if ($kandidatVector) {
                    $score = CosineSimilarity::calculate($inputVector, $kandidatVector);
                    
                    if ($score >= 0.75) {
                        if ($tertinggi === null || $score > ($tertinggi['raw_score'] ?? 0)) {
                            $tertinggi = [
                                'surat_id'      => $kandidat->surat_id,
                                'nomor_surat'   => $kandidat->nomor_surat ?? '(belum bernomor)',
                                'nama_kegiatan' => $kandidat->nama_kegiatan,
                                'tanggal_mulai' => $kandidat->tanggal_mulai instanceof Carbon
                                    ? $kandidat->tanggal_mulai->toDateString()
                                    : (string) $kandidat->tanggal_mulai,
                                'percent'       => round($score * 100, 1),
                                'raw_score'     => $score, // Internal tracking
                            ];
                        }
                    }
                }
            }
            
            // Hapus raw_score sebelum return
            if ($tertinggi !== null) {
                unset($tertinggi['raw_score']);
            }
        } else {
            // Fallback: AI Service down/not configured -> gunakan similar_text bawaan PHP
            Log::info('Embedding service failed or unavailable. Falling back to similar_text for duplication check.');
            
            $inputNormal = strtolower(trim($namaKegiatan));

            foreach ($kandidats as $kandidat) {
                $kandidatNormal = strtolower(trim($kandidat->nama_kegiatan));

                // similar_text() mengisi $percent via pass-by-reference
                similar_text($inputNormal, $kandidatNormal, $percent);

                if ($percent >= 70.0) {
                    if ($tertinggi === null || $percent > $tertinggi['percent']) {
                        $tertinggi = [
                            'surat_id'      => $kandidat->surat_id,
                            'nomor_surat'   => $kandidat->nomor_surat ?? '(belum bernomor)',
                            'nama_kegiatan' => $kandidat->nama_kegiatan,
                            'tanggal_mulai' => $kandidat->tanggal_mulai instanceof Carbon
                                ? $kandidat->tanggal_mulai->toDateString()
                                : (string) $kandidat->tanggal_mulai,
                            'percent'       => round($percent, 1),
                        ];
                    }
                }
            }
        }

        return $tertinggi;
    }

    // ══════════════════════════════════════════════════════════════════════
    // 2. DETEKSI KONFLIK JADWAL
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Cek apakah lokasi yang sama sudah digunakan oleh kegiatan lain pada rentang tanggal yang tumpang tindih.
     *
     * Konflik LINTAS ORGANISASI — use case utama: dua organisasi berbeda yang
     * mengajukan kegiatan di tempat yang sama pada waktu yang bersamaan.
     *
     * Normalisasi lokasi: trim + lowercase, dibandingkan dengan LOWER(TRIM(lokasi)) di DB.
     *
     * Overlap interval logic:
     *   A overlap B  ⟺  A.mulai <= B.selesai  AND  A.selesai >= B.mulai
     *   Jika tanggal_selesai NULL, dianggap = tanggal_mulai (kegiatan satu hari).
     *
     * @param  string      $lokasi          Lokasi yang akan dicek
     * @param  string      $tanggalMulai    Format Y-m-d
     * @param  string|null $tanggalSelesai  Format Y-m-d, nullable = kegiatan satu hari
     * @param  int|null    $excludeSuratId  ID surat yang dikecualikan (untuk keperluan edit)
     * @return array|null  ['surat_id', 'organisasi_nama', 'nomor_surat', 'nama_kegiatan',
     *                      'tanggal_mulai', 'tanggal_selesai']
     *                     atau null jika tidak ada konflik
     */
    public function cekKonflikJadwal(
        string  $lokasi,
        string  $tanggalMulai,
        ?string $tanggalSelesai = null,
        ?int    $excludeSuratId = null
    ): ?array {
        // Tanggal selesai efektif: jika null, anggap kegiatan satu hari
        $tanggalSelesaiEfektif = $tanggalSelesai ?? $tanggalMulai;

        // Normalisasi lokasi untuk perbandingan case-insensitive + trim whitespace
        $lokasiNormal = strtolower(trim($lokasi));

        $query = SuratKegiatanDetail::query()
            ->join('surats', 'surats.id', '=', 'surat_kegiatan_details.surat_id')
            ->join('organisasis', 'organisasis.id', '=', 'surats.organisasi_id')
            ->whereIn('surats.status', self::ACTIVE_STATUSES)
            // Normalisasi lokasi di sisi DB: LOWER(TRIM(...))
            ->whereRaw('LOWER(TRIM(surat_kegiatan_details.lokasi)) = ?', [$lokasiNormal])
            // Overlap interval:
            //   existing.tanggal_mulai  <= $tanggalSelesaiEfektif
            //   COALESCE(existing.tanggal_selesai, existing.tanggal_mulai) >= $tanggalMulai
            ->where('surat_kegiatan_details.tanggal_mulai', '<=', $tanggalSelesaiEfektif)
            ->whereRaw(
                'COALESCE(surat_kegiatan_details.tanggal_selesai, surat_kegiatan_details.tanggal_mulai) >= ?',
                [$tanggalMulai]
            )
            ->select([
                'surats.id              as surat_id',
                'surats.nomor_surat     as nomor_surat',
                'organisasis.nama       as organisasi_nama',
                'surat_kegiatan_details.nama_kegiatan',
                'surat_kegiatan_details.tanggal_mulai',
                'surat_kegiatan_details.tanggal_selesai',
            ]);

        // Kecualikan surat yang sedang diedit
        if ($excludeSuratId !== null) {
            $query->where('surats.id', '!=', $excludeSuratId);
        }

        $konflik = $query->first();

        if (!$konflik) {
            return null;
        }

        return [
            'surat_id'       => $konflik->surat_id,
            'organisasi_nama'=> $konflik->organisasi_nama,
            'nomor_surat'    => $konflik->nomor_surat ?? '(belum bernomor)',
            'nama_kegiatan'  => $konflik->nama_kegiatan,
            'tanggal_mulai'  => $konflik->tanggal_mulai instanceof Carbon
                ? $konflik->tanggal_mulai->toDateString()
                : (string) $konflik->tanggal_mulai,
            'tanggal_selesai'=> $konflik->tanggal_selesai
                ? ($konflik->tanggal_selesai instanceof Carbon
                    ? $konflik->tanggal_selesai->toDateString()
                    : (string) $konflik->tanggal_selesai)
                : null,
        ];
    }
}
