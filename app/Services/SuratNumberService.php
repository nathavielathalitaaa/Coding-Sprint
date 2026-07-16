<?php

namespace App\Services;

use App\Models\SuratType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SuratNumberService
{
    // ══════════════════════════════════════════════════════════════════
    // GENERATE — increment counter + simpan ke DB (ATOMIK)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Generate nomor surat berikutnya dan increment counter secara atomik.
     *
     * Logic reset counter:
     *   'yearly'  → reset ke 0 jika tahun sekarang ≠ tahun nomor_counter_reset_at
     *   'monthly' → reset ke 0 jika bulan+tahun sekarang ≠ bulan+tahun nomor_counter_reset_at
     *   'never'   → tidak pernah reset, counter terus bertambah
     *
     * Dipanggil di dalam DB::transaction() caller (SuratController::verifikasiAdmin)
     * agar counter tidak nyangkut jika update surat gagal.
     *
     * @throws \RuntimeException jika suratType tidak punya nomor_format
     */
    public function generate(SuratType $suratType): string
    {
        // Kunci baris untuk mencegah race condition concurrent request
        $suratType = SuratType::lockForUpdate()->find($suratType->id);

        $now    = now();
        $counter = $this->resolveCounter($suratType, $now);

        $nomor = $this->buildNomor($suratType, $counter, $now);

        // Simpan counter + reset_at
        $suratType->update([
            'nomor_counter'          => $counter,
            'nomor_counter_reset_at' => $now->toDateString(),
        ]);

        return $nomor;
    }

    // ══════════════════════════════════════════════════════════════════
    // PREVIEW — baca counter+1 TANPA increment (aman dipanggil berkali-kali)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Preview nomor yang AKAN diberikan tanpa menyentuh database.
     * Aman dipanggil berkali-kali untuk keperluan tampilan UI.
     */
    public function previewNext(SuratType $suratType): string
    {
        $now = now();

        // Simulasi counter — sama persis dengan resolveCounter() tapi tidak ada UPDATE
        $counterSaatIni = $suratType->nomor_counter;
        $resetAt        = $suratType->nomor_counter_reset_at
                            ? Carbon::parse($suratType->nomor_counter_reset_at)
                            : null;

        $counterSimulasi = match ($suratType->nomor_reset) {
            'yearly'  => $this->shouldResetYearly($resetAt, $now)
                            ? 1
                            : $counterSaatIni + 1,
            'monthly' => $this->shouldResetMonthly($resetAt, $now)
                            ? 1
                            : $counterSaatIni + 1,
            default   => $counterSaatIni + 1, // 'never'
        };

        return $this->buildNomor($suratType, $counterSimulasi, $now);
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    /**
     * Tentukan nilai counter berikutnya dengan mempertimbangkan kebijakan reset.
     * Dipanggil dari dalam lockForUpdate() transaction.
     */
    private function resolveCounter(SuratType $suratType, Carbon $now): int
    {
        $resetAt = $suratType->nomor_counter_reset_at
                    ? Carbon::parse($suratType->nomor_counter_reset_at)
                    : null;

        return match ($suratType->nomor_reset) {
            'yearly'  => $this->shouldResetYearly($resetAt, $now)
                            ? 1
                            : $suratType->nomor_counter + 1,
            'monthly' => $this->shouldResetMonthly($resetAt, $now)
                            ? 1
                            : $suratType->nomor_counter + 1,
            default   => $suratType->nomor_counter + 1, // 'never'
        };
    }

    /** Reset tahunan: tahun berbeda dari tahun saat ini, atau belum pernah di-set */
    private function shouldResetYearly(?Carbon $resetAt, Carbon $now): bool
    {
        if ($resetAt === null) return false; // surat type baru — mulai dari 1
        return $resetAt->year !== $now->year;
    }

    /** Reset bulanan: bulan+tahun berbeda dari sekarang, atau belum pernah di-set */
    private function shouldResetMonthly(?Carbon $resetAt, Carbon $now): bool
    {
        if ($resetAt === null) return false;
        return $resetAt->year !== $now->year || $resetAt->month !== $now->month;
    }

    /**
     * Render string nomor surat dari nomor_format + counter + tanggal.
     *
     * Token yang didukung:
     *   NOMOR_URUT   → counter di-pad 3 digit (001, 002, ...)
     *   KODE_SURAT   → kode SuratType (huruf kapital)
     *   LEMBAGA      → nilai custom di 'value' (OSIS, MPK, dst)
     *   BULAN_ROMAWI → bulan dalam angka Romawi (I–XII)
     *   TAHUN        → 4 digit tahun
     *   CUSTOM       → nilai bebas di 'value'
     */
    private function buildNomor(SuratType $suratType, int $counter, Carbon $now): string
    {
        if (empty($suratType->nomor_format)) {
            throw new \RuntimeException(
                "SuratType [{$suratType->kode}] tidak punya nomor_format yang valid."
            );
        }

        $bulanRomawi = ['I','II','III','IV','V','VI',
                        'VII','VIII','IX','X','XI','XII'][$now->month - 1];

        $parts = [];
        foreach ($suratType->nomor_format as $komponen) {
            $bagian = match ($komponen['type'] ?? '') {
                'NOMOR_URUT'   => str_pad($counter, 3, '0', STR_PAD_LEFT),
                'KODE_SURAT'   => match (true) {
                    str_starts_with($suratType->kode, 'proposal_')     => 'PROP',
                    str_starts_with($suratType->kode, 'surat_resmi_')   => 'SR',
                    str_starts_with($suratType->kode, 'administrasi_')  => 'ADM',
                    default                                            => strtoupper($suratType->kode),
                },
                'LEMBAGA'      => $komponen['value'] ?? '',
                'BULAN_ROMAWI' => $bulanRomawi,
                'TAHUN'        => (string) $now->year,
                'CUSTOM'       => $komponen['value'] ?? '',
                default        => '',
            };

            if ($bagian !== '') {
                $parts[] = $bagian;
            }
        }

        return implode('/', $parts);
    }
}
