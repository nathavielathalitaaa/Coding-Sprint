<?php

namespace Database\Seeders;

use App\Models\Surat;
use App\Models\SuratType;
use App\Models\Organisasi;
use App\Models\User;
use App\Models\SuratKegiatanDetail;
use App\Models\DocumentApproval;
use App\Models\LaporanPertanggungjawaban;
use App\Models\LpjLampiran;
use App\Models\ProgressUpdate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LpjPracticeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting LPJ Practice seeding...');

        // 1. Ambil data master
        $osis = Organisasi::where('tipe', 'osis')->first();
        if (!$osis) {
            $this->command->error('Organisasi OSIS tidak ditemukan. Silakan jalankan DataAwalSeeder & OrganisasiSeeder terlebih dahulu.');
            return;
        }

        $suratType = SuratType::where('kode', 'proposal_osis')->first();
        if (!$suratType) {
            $this->command->error('Jenis surat proposal_osis tidak ditemukan. Silakan jalankan SuratTypeSeeder terlebih dahulu.');
            return;
        }

        // Ambil User
        $ketuaOsis = User::where('email', 'ketua.osis@smktelkom-sdj.sch.id')->first();
        $bphOsis = User::where('email', 'bph.osis1@smktelkom-sdj.sch.id')->first();
        $bphMpk = User::where('email', 'bph.mpk1@smktelkom-sdj.sch.id')->first();
        $pembinaOsis = User::where('email', 'pembina.osis@smktelkom-sdj.sch.id')->first();
        $pengawasPusat = User::where('email', 'pengawas.pusat@smktelkom-sdj.sch.id')->first();
        $kepkep = User::where('email', 'kepsek@smktelkom-sdj.sch.id')->first();

        if (!$ketuaOsis || !$bphOsis || !$pembinaOsis || !$kepkep) {
            $this->command->error('User demo tidak lengkap. Pastikan DataAwalSeeder & OrganisasiSeeder sudah dijalankan.');
            return;
        }

        // TTD Coordinates default
        $coords = [
            'bph_osis' => ['x' => 20, 'y' => 80, 'page' => 1],
            'bph_mpk' => ['x' => 40, 'y' => 80, 'page' => 1],
            'pembina' => ['x' => 60, 'y' => 80, 'page' => 1],
            'kepala_sekolah' => ['x' => 80, 'y' => 80, 'page' => 1]
        ];

        // Hapus data praktik LPJ sebelumnya agar tidak konflik / duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $suratIdsToDelete = Surat::whereIn('perihal', [
            'Praktek LPJ Case A: Kegiatan Berjalan (Ready to Tandai Selesai)',
            'Praktek LPJ Case B: Draft LPJ Siap Diisi (Ready to Fill)',
            'Praktek LPJ Case C: LPJ Menunggu Verifikasi Pembina (Ready to Verify)'
        ])->pluck('id');
        if ($suratIdsToDelete->isNotEmpty()) {
            Surat::destroy($suratIdsToDelete);
            LaporanPertanggungjawaban::whereIn('surat_id', $suratIdsToDelete)->delete();
            SuratKegiatanDetail::whereIn('surat_id', $suratIdsToDelete)->delete();
            DocumentApproval::whereIn('document_id', $suratIdsToDelete)->where('document_type', 'surat_proposal_osis')->delete();
            ProgressUpdate::whereIn('surat_id', $suratIdsToDelete)->delete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ==========================================
        // CASE A: Proposal Approved, status_pelaksanaan = berjalan, progres 50%
        // ==========================================
        $suratA = Surat::create([
            'user_id' => $ketuaOsis->id,
            'surat_type_id' => $suratType->id,
            'organisasi_id' => $osis->id,
            'nomor_surat' => '001/P-OSIS/A/' . date('Y'),
            'jenis_surat' => 'proposal_osis',
            'perihal' => 'Praktek LPJ Case A: Kegiatan Berjalan (Ready to Tandai Selesai)',
            'file_pdf' => 'surat/dummy.pdf',
            'status' => 'approved_owner',
            'pic_user_id' => $ketuaOsis->id,
            'status_pelaksanaan' => 'berjalan',
            'ttd_coordinates' => $coords,
        ]);

        SuratKegiatanDetail::create([
            'surat_id' => $suratA->id,
            'nama_kegiatan' => 'Praktek LPJ Case A (LDK OSIS)',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(4),
            'lokasi' => 'Aula Sekolah',
            'deskripsi_singkat' => 'Kegiatan praktek LDK OSIS untuk demo LPJ Case A.',
        ]);

        ProgressUpdate::create([
            'surat_id' => $suratA->id,
            'user_id' => $ketuaOsis->id,
            'persentase' => 50,
            'catatan' => 'Persiapan logistik dan koordinasi panitia sudah 50%.',
        ]);

        $this->seedApprovalsForSurat($suratA, $bphOsis, $bphMpk, $pembinaOsis, $pengawasPusat, $kepkep);

        // ==========================================
        // CASE B: Proposal Approved, status_pelaksanaan = selesai, LPJ Draft Created
        // ==========================================
        $suratB = Surat::create([
            'user_id' => $ketuaOsis->id,
            'surat_type_id' => $suratType->id,
            'organisasi_id' => $osis->id,
            'nomor_surat' => '002/P-OSIS/B/' . date('Y'),
            'jenis_surat' => 'proposal_osis',
            'perihal' => 'Praktek LPJ Case B: Draft LPJ Siap Diisi (Ready to Fill)',
            'file_pdf' => 'surat/dummy.pdf',
            'status' => 'approved_owner',
            'pic_user_id' => $ketuaOsis->id,
            'status_pelaksanaan' => 'selesai',
            'ttd_coordinates' => $coords,
        ]);

        SuratKegiatanDetail::create([
            'surat_id' => $suratB->id,
            'nama_kegiatan' => 'Praktek LPJ Case B (Pensi OSIS)',
            'tanggal_mulai' => now()->subDays(5),
            'tanggal_selesai' => now()->subDays(3),
            'lokasi' => 'Lapangan Utama Sekolah',
            'deskripsi_singkat' => 'Kegiatan pensi OSIS untuk demo LPJ Case B.',
        ]);

        ProgressUpdate::create([
            'surat_id' => $suratB->id,
            'user_id' => $ketuaOsis->id,
            'persentase' => 100,
            'catatan' => 'Kegiatan selesai dilaksanakan secara keseluruhan.',
        ]);

        $this->seedApprovalsForSurat($suratB, $bphOsis, $bphMpk, $pembinaOsis, $pengawasPusat, $kepkep);

        LaporanPertanggungjawaban::create([
            'surat_id' => $suratB->id,
            'ringkasan_kegiatan' => 'Kegiatan pensi OSIS telah selesai dilaksanakan dengan lancar.',
            'realisasi_anggaran' => [],
            'status' => 'draft',
        ]);

        // ==========================================
        // CASE C: LPJ Submitted, waiting for Pembina OSIS verification
        // ==========================================
        $suratC = Surat::create([
            'user_id' => $ketuaOsis->id,
            'surat_type_id' => $suratType->id,
            'organisasi_id' => $osis->id,
            'nomor_surat' => '003/P-OSIS/C/' . date('Y'),
            'jenis_surat' => 'proposal_osis',
            'perihal' => 'Praktek LPJ Case C: LPJ Menunggu Verifikasi Pembina (Ready to Verify)',
            'file_pdf' => 'surat/dummy.pdf',
            'status' => 'approved_owner',
            'pic_user_id' => $ketuaOsis->id,
            'status_pelaksanaan' => 'selesai',
            'ttd_coordinates' => $coords,
        ]);

        SuratKegiatanDetail::create([
            'surat_id' => $suratC->id,
            'nama_kegiatan' => 'Praktek LPJ Case C (Bakti Sosial OSIS)',
            'tanggal_mulai' => now()->subDays(10),
            'tanggal_selesai' => now()->subDays(9),
            'lokasi' => 'Panti Asuhan terdekat',
            'deskripsi_singkat' => 'Kegiatan bakti sosial OSIS untuk demo LPJ Case C.',
        ]);

        ProgressUpdate::create([
            'surat_id' => $suratC->id,
            'user_id' => $ketuaOsis->id,
            'persentase' => 100,
            'catatan' => 'Kegiatan baksos selesai dilaksanakan dan pembagian sembako berhasil.',
        ]);

        $this->seedApprovalsForSurat($suratC, $bphOsis, $bphMpk, $pembinaOsis, $pengawasPusat, $kepkep);

        $lpjC = LaporanPertanggungjawaban::create([
            'surat_id' => $suratC->id,
            'ringkasan_kegiatan' => 'Kegiatan Bakti Sosial berjalan lancar. Seluruh sembako telah diserahkan langsung ke panti asuhan.',
            'realisasi_anggaran' => [
                ['item' => 'Sembako & Konsumsi', 'jumlah' => 1500000],
                ['item' => 'Sewa Transportasi', 'jumlah' => 500000],
                ['item' => 'Banner & Dokumentasi', 'jumlah' => 150000]
            ],
            'status' => 'submitted',
        ]);

        LpjLampiran::create([
            'lpj_id' => $lpjC->id,
            'file_path' => 'lpj_lampirans/dummy_foto.jpg',
            'tipe' => 'foto',
            'keterangan' => 'Foto Penyerahan Sembako',
        ]);

        LpjLampiran::create([
            'lpj_id' => $lpjC->id,
            'file_path' => 'lpj_lampirans/dummy_nota.jpg',
            'tipe' => 'kwitansi',
            'keterangan' => 'Nota Pembelian Sembako',
        ]);

        $this->command->info('Seeded Case A, Case B, and Case C successfully!');
    }

    private function seedApprovalsForSurat($surat, $bphOsis, $bphMpk, $pembinaOsis, $pengawasPusat, $kepkep)
    {
        $steps = [
            ['step_order' => 1, 'jabatan' => 'bph_osis', 'target_mode' => 'submitter', 'label' => 'Diajukan BPH OSIS', 'is_signer' => true, 'user_id' => $bphOsis->id],
            ['step_order' => 2, 'jabatan' => 'bph_mpk', 'target_mode' => 'fixed_mpk', 'label' => 'Disetujui BPH MPK', 'is_signer' => false, 'user_id' => $bphMpk->id],
            ['step_order' => 3, 'jabatan' => 'pembina', 'target_mode' => 'submitter', 'label' => 'Disetujui Pembina OSIS', 'is_signer' => true, 'user_id' => $pembinaOsis->id],
            ['step_order' => 4, 'jabatan' => 'pengawas_pusat', 'target_mode' => 'global', 'label' => 'Disetujui Pengawas Pusat', 'is_signer' => false, 'user_id' => $pengawasPusat->id],
            ['step_order' => 5, 'jabatan' => 'kepala_sekolah', 'target_mode' => 'global', 'label' => 'Disetujui Kepala Sekolah', 'is_signer' => true, 'user_id' => $kepkep->id]
        ];

        foreach ($steps as $step) {
            DocumentApproval::create([
                'document_type' => 'surat_proposal_osis',
                'document_id' => $surat->id,
                'step_order' => $step['step_order'],
                'jabatan' => $step['jabatan'],
                'target_mode' => $step['target_mode'],
                'surat_organisasi_id' => $surat->organisasi_id,
                'label' => $step['label'],
                'metode_ttd' => 'stamp',
                'is_signer' => $step['is_signer'],
                'approver_id' => $step['user_id'],
                'status' => 'approved',
                'actioned_at' => now()->subHours(6 - $step['step_order']),
                'catatan' => 'Auto-approved by LPJ practice seeder',
            ]);
        }
    }
}
