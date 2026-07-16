<?php

namespace Database\Seeders;

use App\Models\LaporanPertanggungjawaban;
use App\Models\LpjLampiran;
use App\Models\OrganisasiMember;
use App\Models\ProgressUpdate;
use App\Models\Surat;
use App\Models\SuratKegiatanDetail;
use App\Models\SuratType;
use App\Models\SuratTypeApprover;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\DocumentApproval;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Surat::count() > 0) {
            $this->command->info('[DemoData] Surat already exists — skipping.');
            return;
        }

        $users = User::all()->keyBy('user_id');
        $suratTypes = SuratType::all()->keyBy('kode');

        // ── Resolve user references ─────────────────────────────────────
        $admin         = $users['ADMIN-001'] ?? null;
        $pengawasPusat = $users['GURU-001'] ?? null;
        $kepsek        = $users['GURU-002'] ?? null;
        $pembinaOsis   = $users['GURU-003'] ?? null;
        $pembinaMpk    = $users['GURU-004'] ?? null;
        $ketuaOsis     = $users['ANT-001'] ?? null;
        $bphOsis1      = $users['ANT-002'] ?? null;
        $ketuaMpk      = $users['ANT-003'] ?? null;
        $bphMpk1       = $users['ANT-004'] ?? null;
        $anggotaRohis  = $users['ANT-005'] ?? null;

        $osis = \App\Models\Organisasi::where('tipe', 'osis')->first();
        $mpk  = \App\Models\Organisasi::where('tipe', 'mpk')->first();
        $rohis = \App\Models\Organisasi::where('tipe', 'sub_organ')->first();
        $komisiReligi = \App\Models\Komisi::first();

        $this->command->info('[DemoData] Creating UserProfiles...');
        $this->seedUserProfiles(compact(
            'admin','pengawasPusat','kepsek','pembinaOsis','pembinaMpk',
            'ketuaOsis','bphOsis1','ketuaMpk','bphMpk1','anggotaRohis'
        ));

        $this->command->info('[DemoData] Creating Surat & related data...');

        DB::transaction(function () use (
            $ketuaOsis, $bphOsis1, $ketuaMpk, $bphMpk1, $anggotaRohis,
            $pembinaOsis, $pembinaMpk, $pengawasPusat, $kepsek,
            $suratTypes, $osis, $mpk, $rohis, $komisiReligi
        ) {
            // ════════════════════════════════════════════════════════════
            // SURAT 1 — Pelantikan Pengurus OSIS (fully approved)
            // ════════════════════════════════════════════════════════════
            $surat1 = $this->createSurat([
                'user_id'          => $ketuaOsis->id,
                'surat_type_id'    => $suratTypes['proposal_osis']->id,
                'organisasi_id'    => $osis->id,
                'nomor_surat'      => '001/PROP/OSIS/VII/2026',
                'jenis_surat'      => 'proposal_osis',
                'perihal'          => 'Permohonan Persetujuan Proposal Pelantikan Pengurus OSIS Masa Bakti 2026/2027',
                'file_pdf'         => 'demo/pelantikan-osis-2026.pdf',
                'status'           => 'approved_owner',
                'cover_pdf_path'   => 'demo/covers/pelantikan-osis-2026-cover.pdf',
                'final_pdf_path'   => 'demo/final/pelantikan-osis-2026-final.pdf',
                'status_pelaksanaan' => 'selesai',
                'pic_user_id'      => $ketuaOsis->id,
            ]);

            $this->createKegiatanDetail($surat1, [
                'nama_kegiatan'    => 'Pelantikan Pengurus OSIS Masa Bakti 2026/2027',
                'tanggal_mulai'    => '2026-08-17',
                'tanggal_selesai'  => '2026-08-17',
                'lokasi'           => 'Aula SMK Telkom Sidoarjo',
                'deskripsi_singkat'=> 'Pelantikan pengurus OSIS baru masa bakti 2026/2027 yang akan dilaksanakan bersamaan dengan peringatan HUT RI ke-81.',
            ]);

            $this->createApprovals($surat1, [
                ['target_mode' => 'submitter',    'step_order' => 1, 'jabatan' => 'bph',            'label' => 'Diajukan BPH OSIS',        'is_signer' => true,  'approver_id' => $ketuaOsis->id,     'status' => 'approved', 'actioned_at' => '2026-07-20 08:00:00'],
                ['target_mode' => 'fixed_mpk',    'step_order' => 2, 'jabatan' => 'bph',            'label' => 'Disetujui BPH MPK',        'is_signer' => false, 'approver_id' => $bphOsis1->id,     'status' => 'approved', 'actioned_at' => '2026-07-20 09:00:00'],
                ['target_mode' => 'submitter',    'step_order' => 3, 'jabatan' => 'pembina',        'label' => 'Disetujui Pembina OSIS',   'is_signer' => true,  'approver_id' => $pembinaOsis->id,   'status' => 'approved', 'actioned_at' => '2026-07-21 10:00:00'],
                ['target_mode' => 'global',       'step_order' => 4, 'jabatan' => 'pengawas_pusat', 'label' => 'Disetujui Pengawas Pusat',  'is_signer' => false, 'approver_id' => $pengawasPusat->id, 'status' => 'approved', 'actioned_at' => '2026-07-22 08:30:00'],
                ['target_mode' => 'global',       'step_order' => 5, 'jabatan' => 'kepala_sekolah', 'label' => 'Disetujui Kepala Sekolah',  'is_signer' => true,  'approver_id' => $kepsek->id,        'status' => 'approved', 'actioned_at' => '2026-07-23 13:00:00'],
            ]);

            $this->createLPJ($surat1, [
                'ringkasan_kegiatan' => 'Kegiatan pelantikan pengurus OSIS masa bakti 2026/2027 telah dilaksanakan pada tanggal 17 Agustus 2026 di Aula SMK Telkom Sidoarjo. Acara berjalan dengan lancar dan dihadiri oleh seluruh pengurus OSIS baru, guru, dan staf sekolah.',
                'realisasi_anggaran' => json_encode([
                    ['item' => 'Sewa Sound System',        'anggaran' => 500000, 'realisasi' => 450000],
                    ['item' => 'Dekorasi Panggung',        'anggaran' => 300000, 'realisasi' => 275000],
                    ['item' => 'Cetak Sertifikat',         'anggaran' => 200000, 'realisasi' => 180000],
                    ['item' => 'Konsumsi Snack',           'anggaran' => 400000, 'realisasi' => 375000],
                    ['item' => 'Dokumentasi',              'anggaran' => 150000, 'realisasi' => 150000],
                ]),
                'status'       => 'valid',
                'verified_by'  => $pembinaOsis->id,
                'verified_at'  => '2026-08-20 14:00:00',
            ], [
                ['file_path' => 'demo/lpj/pelantikan-foto1.jpg', 'tipe' => 'foto',    'keterangan' => 'Foto kegiatan pelantikan'],
                ['file_path' => 'demo/lpj/pelantikan-foto2.jpg', 'tipe' => 'foto',    'keterangan' => 'Foto sesi dokumentasi'],
                ['file_path' => 'demo/lpj/kwitansi-sound.pdf',   'tipe' => 'kwitansi', 'keterangan' => 'Kwitansi sewa sound system'],
            ]);

            $this->createProgressUpdates($surat1, [
                ['user_id' => $bphOsis1->id, 'persentase' => 25, 'catatan' => 'Persiapan konsep acara dan pembentukan panitia.'],
                ['user_id' => $bphOsis1->id, 'persentase' => 50, 'catatan' => 'Publikasi undangan dan koordinasi dengan vendor sound.'],
                ['user_id' => $bphOsis1->id, 'persentase' => 75, 'catatan' => 'Gladi bersih dan pengecekan akhir seluruh perlengkapan.'],
                ['user_id' => $bphOsis1->id, 'persentase' => 100, 'catatan' => 'Kegiatan selesai dilaksanakan. LPJ dalam proses.'],
            ]);

            // ════════════════════════════════════════════════════════════
            // SURAT 2 — Bakti Sosial Ramadan (waiting step 5 — kepala_sekolah)
            // ════════════════════════════════════════════════════════════
            $surat2 = $this->createSurat([
                'user_id'          => $bphOsis1->id,
                'surat_type_id'    => $suratTypes['proposal_osis']->id,
                'organisasi_id'    => $osis->id,
                'nomor_surat'      => '002/PROP/OSIS/VII/2026',
                'jenis_surat'      => 'proposal_osis',
                'perihal'          => 'Permohonan Persetujuan Proposal Bakti Sosial Ramadan 1447 H',
                'file_pdf'         => 'demo/baksos-ramadan-2026.pdf',
                'status'           => 'submitted',
                'status_pelaksanaan' => 'belum_mulai',
                'pic_user_id'      => $bphOsis1->id,
            ]);

            $this->createKegiatanDetail($surat2, [
                'nama_kegiatan'    => 'Bakti Sosial Ramadan 1447 H',
                'tanggal_mulai'    => '2026-03-10',
                'tanggal_selesai'  => '2026-03-12',
                'lokasi'           => 'Panti Asuhan Al-Ikhlas Sidoarjo',
                'deskripsi_singkat'=> 'Kegiatan bakti sosial berupa pemberian santunan dan buka puasa bersama anak-anak panti asuhan.',
            ]);

            $this->createApprovals($surat2, [
                ['target_mode' => 'submitter',    'step_order' => 1, 'jabatan' => 'bph',            'label' => 'Diajukan BPH OSIS',        'is_signer' => true,  'approver_id' => $bphOsis1->id,     'status' => 'approved', 'actioned_at' => '2026-07-25 09:00:00'],
                ['target_mode' => 'fixed_mpk',    'step_order' => 2, 'jabatan' => 'bph',            'label' => 'Disetujui BPH MPK',        'is_signer' => false, 'approver_id' => $bphMpk1->id,      'status' => 'approved', 'actioned_at' => '2026-07-25 10:30:00'],
                ['target_mode' => 'submitter',    'step_order' => 3, 'jabatan' => 'pembina',        'label' => 'Disetujui Pembina OSIS',   'is_signer' => true,  'approver_id' => $pembinaOsis->id,   'status' => 'approved', 'actioned_at' => '2026-07-26 08:00:00'],
                ['target_mode' => 'global',       'step_order' => 4, 'jabatan' => 'pengawas_pusat', 'label' => 'Disetujui Pengawas Pusat',  'is_signer' => false, 'approver_id' => $pengawasPusat->id, 'status' => 'approved', 'actioned_at' => '2026-07-27 11:00:00'],
                ['target_mode' => 'global',       'step_order' => 5, 'jabatan' => 'kepala_sekolah', 'label' => 'Disetujui Kepala Sekolah',  'is_signer' => true,  'approver_id' => null,               'status' => 'waiting',  'actioned_at' => null],
            ]);

            // ════════════════════════════════════════════════════════════
            // SURAT 3 — Undangan Rapat Koordinasi (just submitted, step 1 waiting)
            // ════════════════════════════════════════════════════════════
            $surat3 = $this->createSurat([
                'user_id'          => $ketuaOsis->id,
                'surat_type_id'    => $suratTypes['surat_resmi_osis']->id,
                'organisasi_id'    => $osis->id,
                'nomor_surat'      => '001/SR/OSIS/VII/2026',
                'jenis_surat'      => 'surat_resmi_osis',
                'perihal'          => 'Undangan Rapat Koordinasi Pengurus OSIS dan MPK',
                'file_pdf'         => 'demo/undangan-rapat.pdf',
                'status'           => 'submitted',
                'status_pelaksanaan' => 'belum_mulai',
            ]);

            $this->createApprovals($surat3, [
                ['target_mode' => 'submitter',    'step_order' => 1, 'jabatan' => 'bph',            'label' => 'Diajukan BPH OSIS',        'is_signer' => true,  'approver_id' => null,               'status' => 'waiting',  'actioned_at' => null],
                ['target_mode' => 'fixed_mpk',    'step_order' => 2, 'jabatan' => 'bph',            'label' => 'Disetujui BPH MPK',        'is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'submitter',    'step_order' => 3, 'jabatan' => 'pembina',        'label' => 'Disetujui Pembina OSIS',   'is_signer' => true,  'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 4, 'jabatan' => 'pengawas_pusat', 'label' => 'Disetujui Pengawas Pusat',  'is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 5, 'jabatan' => 'kepala_sekolah', 'label' => 'Disetujui Kepala Sekolah',  'is_signer' => true,  'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
            ]);

            // ════════════════════════════════════════════════════════════
            // SURAT 4 — Sosialisasi Tata Tertib (rejected by Pembina MPK)
            // ════════════════════════════════════════════════════════════
            $surat4 = $this->createSurat([
                'user_id'          => $ketuaMpk->id,
                'surat_type_id'    => $suratTypes['proposal_mpk']->id,
                'organisasi_id'    => $mpk->id,
                'komisi_id'        => $komisiReligi?->id,
                'nomor_surat'      => '001/PROP/MPK/VII/2026',
                'jenis_surat'      => 'proposal_mpk',
                'perihal'          => 'Permohonan Persetujuan Proposal Sosialisasi Tata Tertib Siswa',
                'file_pdf'         => 'demo/sosialisasi-tatib.pdf',
                'status'           => 'revised',
                'catatan_revisi'   => 'Lingkup kegiatan terlalu luas. Fokuskan pada satu aspek saja, misalnya kedisiplinan berpakaian. Sertakan jadwal yang lebih rinci.',
                'status_pelaksanaan' => 'belum_mulai',
            ]);

            $this->createKegiatanDetail($surat4, [
                'nama_kegiatan'    => 'Sosialisasi Tata Tertib Siswa',
                'tanggal_mulai'    => '2026-08-01',
                'tanggal_selesai'  => '2026-08-02',
                'lokasi'           => 'Aula SMK Telkom Sidoarjo',
                'deskripsi_singkat'=> 'Sosialisasi tata tertib sekolah kepada siswa kelas X.',
            ]);

            $this->createApprovals($surat4, [
                ['target_mode' => 'submitter',    'step_order' => 1, 'jabatan' => 'bph',            'label' => 'Diajukan BPH MPK',         'is_signer' => true,  'approver_id' => $ketuaMpk->id,     'status' => 'approved', 'actioned_at' => '2026-07-22 10:00:00'],
                ['target_mode' => 'submitter',    'step_order' => 2, 'jabatan' => 'komisi',         'label' => 'Disetujui Komisi MPK',     'is_signer' => false, 'approver_id' => $anggotaRohis->id, 'status' => 'approved', 'actioned_at' => '2026-07-22 11:00:00'],
                ['target_mode' => 'submitter',    'step_order' => 3, 'jabatan' => 'pembina',        'label' => 'Disetujui Pembina MPK',    'is_signer' => true,  'approver_id' => $pembinaMpk->id,   'status' => 'rejected', 'actioned_at' => '2026-07-23 09:00:00', 'catatan' => 'Lingkup kegiatan terlalu luas. Fokuskan pada satu aspek saja, misalnya kedisiplinan berpakaian. Sertakan jadwal yang lebih rinci.'],
                ['target_mode' => 'global',       'step_order' => 4, 'jabatan' => 'pengawas_pusat', 'label' => 'Disetujui Pengawas Pusat',  'is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 5, 'jabatan' => 'kepala_sekolah', 'label' => 'Disetujui Kepala Sekolah',  'is_signer' => true,  'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
            ]);

            // ════════════════════════════════════════════════════════════
            // SURAT 5 — Kajian Rutin Rohani (submitted, sub_organ)
            // ════════════════════════════════════════════════════════════
            $surat5 = $this->createSurat([
                'user_id'          => $anggotaRohis->id,
                'surat_type_id'    => $suratTypes['proposal_sub_organ']->id,
                'organisasi_id'    => $rohis->id,
                'nomor_surat'      => '001/PROP/SUB/VII/2026',
                'jenis_surat'      => 'proposal_sub_organ',
                'perihal'          => 'Permohonan Persetujuan Proposal Kajian Rutin Rohani Semester Gasal',
                'file_pdf'         => 'demo/kajian-rohis.pdf',
                'status'           => 'submitted',
                'status_pelaksanaan' => 'belum_mulai',
            ]);

            $this->createKegiatanDetail($surat5, [
                'nama_kegiatan'    => 'Kajian Rutin Rohani Semester Gasal',
                'tanggal_mulai'    => '2026-08-05',
                'tanggal_selesai'  => '2026-12-15',
                'lokasi'           => 'Masjid Al-Hikmah SMK Telkom Sidoarjo',
                'deskripsi_singkat'=> 'Kajian rutin setiap hari Jumat pagi untuk siswa muslim, dengan tema-tema keislaman dan pembentukan karakter.',
            ]);

            $this->createApprovals($surat5, [
                ['target_mode' => 'submitter',    'step_order' => 1, 'jabatan' => 'bph',            'label' => 'Diajukan BPH Sub Organ',     'is_signer' => true,  'approver_id' => null,               'status' => 'waiting',  'actioned_at' => null],
                ['target_mode' => 'fixed_osis',   'step_order' => 2, 'jabatan' => 'bph',            'label' => 'Disetujui BPH OSIS',         'is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'fixed_mpk',    'step_order' => 3, 'jabatan' => 'bph',            'label' => 'Disetujui BPH MPK',          'is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'submitter',    'step_order' => 4, 'jabatan' => 'pembina',        'label' => 'Disetujui Pembina Sub Organ', 'is_signer' => true,  'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'submitter',    'step_order' => 5, 'jabatan' => 'pengawas',       'label' => 'Disetujui Pengawas Sub Organ','is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 6, 'jabatan' => 'pengawas_pusat', 'label' => 'Disetujui Pengawas Pusat',   'is_signer' => false, 'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 7, 'jabatan' => 'kepala_sekolah', 'label' => 'Disetujui Kepala Sekolah',   'is_signer' => true,  'approver_id' => null,               'status' => 'pending',  'actioned_at' => null],
            ]);

            // ════════════════════════════════════════════════════════════
            // SURAT 6 — Surat Izin Kegiatan Ekstrakurikuler (MPK, submitted) ═══
            // ════════════════════════════════════════════════════════════
            $surat6 = $this->createSurat([
                'user_id'          => $bphMpk1->id,
                'surat_type_id'    => $suratTypes['surat_resmi_mpk']->id,
                'organisasi_id'    => $mpk->id,
                'komisi_id'        => $komisiReligi?->id,
                'nomor_surat'      => '001/SR/MPK/VII/2026',
                'jenis_surat'      => 'surat_resmi_mpk',
                'perihal'          => 'Permohonan Izin Kegiatan Ekstrakurikuler Semester Gasal',
                'file_pdf'         => 'demo/izin-ekstrakurikuler.pdf',
                'status'           => 'submitted',
                'status_pelaksanaan' => 'belum_mulai',
            ]);

            $this->createApprovals($surat6, [
                ['target_mode' => 'submitter',    'step_order' => 1, 'jabatan' => 'bph',            'label' => 'Diajukan BPH MPK',         'is_signer' => true,  'approver_id' => $bphMpk1->id, 'status' => 'approved', 'actioned_at' => '2026-07-28 08:00:00'],
                ['target_mode' => 'submitter',    'step_order' => 2, 'jabatan' => 'komisi',         'label' => 'Disetujui Komisi MPK',     'is_signer' => false, 'approver_id' => null,          'status' => 'waiting',  'actioned_at' => null],
                ['target_mode' => 'submitter',    'step_order' => 3, 'jabatan' => 'pembina',        'label' => 'Disetujui Pembina MPK',    'is_signer' => true,  'approver_id' => null,          'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 4, 'jabatan' => 'pengawas_pusat', 'label' => 'Disetujui Pengawas Pusat',  'is_signer' => false, 'approver_id' => null,          'status' => 'pending',  'actioned_at' => null],
                ['target_mode' => 'global',       'step_order' => 5, 'jabatan' => 'kepala_sekolah', 'label' => 'Disetujui Kepala Sekolah',  'is_signer' => true,  'approver_id' => null,          'status' => 'pending',  'actioned_at' => null],
            ]);
        });

        // ═══════════════════════════════════════════════════════════════
        // Notifications & Activity Logs (di luar transaction agar tetap
        // tersimpan walau surat gagal)
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('[DemoData] Creating notifications...');
        $this->seedNotifications($users);

        $this->command->info('[DemoData] Creating activity logs...');
        $this->seedActivityLogs($users);

        $this->command->info('[DemoData] Done! Created 6 surat with complete demo data.');
    }

    // ── Helper: create surat ─────────────────────────────────────────────
    private function createSurat(array $data): Surat
    {
        $surat = Surat::create($data);
        // Update created_at/updated_at to a realistic past date
        $surat->timestamps = false;
        $surat->update([
            'created_at' => $data['created_at'] ?? now()->subDays(rand(5, 30)),
            'updated_at' => now(),
        ]);
        $surat->timestamps = true;
        return $surat;
    }

    // ── Helper: create kegiatan detail ───────────────────────────────────
    private function createKegiatanDetail(Surat $surat, array $data): void
    {
        SuratKegiatanDetail::create(array_merge(
            ['surat_id' => $surat->id],
            $data
        ));
    }

    // ── Helper: create approval steps with target_mode inheritance ───────
    private function createApprovals(Surat $surat, array $steps): void
    {
        foreach ($steps as $s) {
            DocumentApproval::create([
                'document_type'       => 'surat_' . $surat->jenis_surat,
                'document_id'         => $surat->id,
                'step_order'          => $s['step_order'],
                'jabatan'             => $s['jabatan'],
                'label'               => $s['label'],
                'target_mode'         => $s['target_mode'],
                'surat_organisasi_id' => $surat->organisasi_id,
                'surat_komisi_id'     => $surat->komisi_id,
                'is_signer'           => $s['is_signer'],
                'metode_ttd'          => $s['is_signer'] ? 'stamp' : 'append',
                'approver_id'         => $s['approver_id'] ?? null,
                'status'              => $s['status'],
                'catatan'             => $s['catatan'] ?? null,
                'actioned_at'         => $s['actioned_at'] ?? null,
                'created_at'          => $s['actioned_at'] ?? now(),
                'updated_at'          => $s['actioned_at'] ?? now(),
            ]);
        }
    }

    // ── Helper: create LPJ + lampiran ───────────────────────────────────
    private function createLPJ(Surat $surat, array $lpjData, array $lampirans): void
    {
        $lpj = LaporanPertanggungjawaban::create(array_merge(
            ['surat_id' => $surat->id],
            $lpjData
        ));

        foreach ($lampirans as $l) {
            LpjLampiran::create([
                'lpj_id'     => $lpj->id,
                'file_path'  => $l['file_path'],
                'tipe'       => $l['tipe'],
                'keterangan' => $l['keterangan'],
            ]);
        }
    }

    // ── Helper: create progress updates ──────────────────────────────────
    private function createProgressUpdates(Surat $surat, array $updates): void
    {
        foreach ($updates as $u) {
            ProgressUpdate::create([
                'surat_id'    => $surat->id,
                'user_id'     => $u['user_id'],
                'persentase'  => $u['persentase'],
                'catatan'     => $u['catatan'],
            ]);
        }
    }

    // ── Seed UserProfiles ────────────────────────────────────────────────
    private function seedUserProfiles(array $u): void
    {
        $profiles = [
            ['user_id' => $u['admin']?->id,         'jabatan_struktural' => 'Administrator Sistem',               'no_telepon' => '081234567890', 'tgl_bergabung' => '2025-01-01', 'alamat' => 'Jl. Raya Sekolah No. 1, Sidoarjo'],
            ['user_id' => $u['pengawasPusat']?->id, 'jabatan_struktural' => 'Pengawas Pusat SMK Telkom',          'no_telepon' => '081234567891', 'tgl_bergabung' => '2025-01-01', 'alamat' => 'Jl. Pendidikan No. 5, Surabaya'],
            ['user_id' => $u['kepsek']?->id,        'jabatan_struktural' => 'Kepala SMK Telkom Sidoarjo',         'no_telepon' => '081234567892', 'tgl_bergabung' => '2024-07-01', 'alamat' => 'Jl. Merdeka No. 10, Sidoarjo'],
            ['user_id' => $u['pembinaOsis']?->id,   'jabatan_struktural' => 'Guru BK / Pembina OSIS',            'no_telepon' => '081234567893', 'tgl_bergabung' => '2025-01-01', 'alamat' => 'Jl. Telkom No. 15, Sidoarjo'],
            ['user_id' => $u['pembinaMpk']?->id,    'jabatan_struktural' => 'Guru PPKn / Pembina MPK',           'no_telepon' => '081234567894', 'tgl_bergabung' => '2025-01-01', 'alamat' => 'Jl. Kemerdekaan No. 20, Sidoarjo'],
            ['user_id' => $u['ketuaOsis']?->id,     'jabatan_struktural' => 'Kelas XI RPL 1',                    'no_telepon' => '081234567895', 'tgl_bergabung' => '2026-01-15', 'alamat' => 'Jl. Anggrek No. 5, Sidoarjo'],
            ['user_id' => $u['bphOsis1']?->id,      'jabatan_struktural' => 'Kelas XI RPL 2',                    'no_telepon' => '081234567896', 'tgl_bergabung' => '2026-01-15', 'alamat' => 'Jl. Mawar No. 8, Sidoarjo'],
            ['user_id' => $u['ketuaMpk']?->id,      'jabatan_struktural' => 'Kelas XI TKJ 1',                    'no_telepon' => '081234567897', 'tgl_bergabung' => '2026-01-15', 'alamat' => 'Jl. Melati No. 12, Sidoarjo'],
            ['user_id' => $u['bphMpk1']?->id,       'jabatan_struktural' => 'Kelas XI TKJ 2',                    'no_telepon' => '081234567898', 'tgl_bergabung' => '2026-01-15', 'alamat' => 'Jl. Kenanga No. 3, Sidoarjo'],
            ['user_id' => $u['anggotaRohis']?->id,  'jabatan_struktural' => 'Kelas X RPL 1',                     'no_telepon' => '081234567899', 'tgl_bergabung' => '2026-07-01', 'alamat' => 'Jl. Dahlia No. 7, Sidoarjo'],
        ];

        foreach ($profiles as $p) {
            if ($p['user_id']) {
                UserProfile::updateOrCreate(
                    ['user_id' => $p['user_id']],
                    $p
                );
            }
        }
    }

    // ── Seed Notifications ───────────────────────────────────────────────
    private function seedNotifications($users): void
    {
        $notifications = [
            [
                'user_id' => $users['GURU-003']->id ?? null,
                'title'   => 'Permintaan Approval Surat Baru',
                'message' => 'Surat "Bakti Sosial Ramadan 1447 H" menunggu persetujuan Anda sebagai Pembina OSIS.',
                'url'     => '/surat',
                'is_read' => false,
            ],
            [
                'user_id' => $users['GURU-003']->id ?? null,
                'title'   => 'Permintaan Approval Surat Baru',
                'message' => 'Surat "Pelantikan Pengurus OSIS" telah diajukan dan menunggu persetujuan Anda.',
                'url'     => '/surat',
                'is_read' => false,
            ],
            [
                'user_id' => $users['ANT-005']->id ?? null,
                'title'   => 'Surat Ditolak / Perlu Revisi',
                'message' => 'Surat "Kajian Rutin Rohani" perlu diperbaiki. Silakan cek catatan revisi.',
                'url'     => '/surat',
                'is_read' => true,
            ],
            [
                'user_id' => $users['ANT-001']->id ?? null,
                'title'   => 'Surat Disetujui Penuh',
                'message' => 'Surat "Pelantikan Pengurus OSIS" telah disetujui sepenuhnya.',
                'url'     => '/surat/1',
                'is_read' => true,
            ],
            [
                'user_id' => $users['ANT-001']->id ?? null,
                'title'   => 'Penugasan PIC Kegiatan',
                'message' => 'Anda ditugaskan sebagai PIC untuk pelaksanaan kegiatan: Pelantikan Pengurus OSIS Masa Bakti 2026/2027.',
                'url'     => '/pelaksanaan',
                'is_read' => false,
            ],
            [
                'user_id' => $users['GURU-001']->id ?? null,
                'title'   => 'Permintaan Approval Surat Baru',
                'message' => 'Surat "Undangan Rapat Koordinasi OSIS" menunggu persetujuan Anda sebagai Pengawas Pusat.',
                'url'     => '/surat',
                'is_read' => false,
            ],
            [
                'user_id' => $users['GURU-004']->id ?? null,
                'title'   => 'Permintaan Approval Surat Baru',
                'message' => 'Surat "Izin Kegiatan Ekstrakurikuler" menunggu persetujuan Anda sebagai Pembina MPK.',
                'url'     => '/surat',
                'is_read' => false,
            ],
        ];

        foreach ($notifications as $n) {
            if ($n['user_id']) {
                Notification::create($n);
            }
        }
    }

    // ── Seed Activity Logs ───────────────────────────────────────────────
    private function seedActivityLogs($users): void
    {
        $logs = [
            ['user_id' => $users['ANT-001']->id, 'action' => 'create',        'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Membuat surat baru: Pelantikan Pengurus OSIS Masa Bakti 2026/2027'],
            ['user_id' => $users['ANT-001']->id, 'action' => 'submit',        'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Mengajukan surat untuk approval'],
            ['user_id' => $users['ANT-001']->id, 'action' => 'approve',       'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Menyetujui step 1 (BPH OSIS)'],
            ['user_id' => $users['ANT-002']->id, 'action' => 'approve',       'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Menyetujui step 2 (BPH MPK)'],
            ['user_id' => $users['GURU-003']->id, 'action' => 'approve',      'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Menyetujui step 3 (Pembina OSIS)'],
            ['user_id' => $users['GURU-001']->id, 'action' => 'approve',      'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Menyetujui step 4 (Pengawas Pusat)'],
            ['user_id' => $users['GURU-002']->id, 'action' => 'approve',      'target_type' => 'Surat', 'target_id' => 1, 'description' => 'Menyetujui step 5 (Kepala Sekolah) – Surat disetujui penuh'],
            ['user_id' => $users['ANT-001']->id, 'action' => 'update_progress','target_type' => 'ProgressUpdate', 'target_id' => 1, 'description' => 'Update progress pelaksanaan: 100%'],
            ['user_id' => $users['ANT-002']->id, 'action' => 'create',        'target_type' => 'Surat', 'target_id' => 2, 'description' => 'Membuat surat baru: Bakti Sosial Ramadan 1447 H'],
            ['user_id' => $users['ANT-001']->id, 'action' => 'create',        'target_type' => 'Surat', 'target_id' => 3, 'description' => 'Membuat surat baru: Undangan Rapat Koordinasi Pengurus OSIS dan MPK'],
            ['user_id' => $users['ANT-003']->id, 'action' => 'create',        'target_type' => 'Surat', 'target_id' => 4, 'description' => 'Membuat surat baru: Sosialisasi Tata Tertib Siswa'],
            ['user_id' => $users['GURU-004']->id, 'action' => 'reject',       'target_type' => 'Surat', 'target_id' => 4, 'description' => 'Menolak surat Sosialisasi Tata Tertib Siswa – perlu revisi'],
            ['user_id' => $users['ANT-005']->id, 'action' => 'create',        'target_type' => 'Surat', 'target_id' => 5, 'description' => 'Membuat surat baru: Kajian Rutin Rohani Semester Gasal'],
            ['user_id' => $users['ANT-004']->id, 'action' => 'create',        'target_type' => 'Surat', 'target_id' => 6, 'description' => 'Membuat surat baru: Izin Kegiatan Ekstrakurikuler Semester Gasal'],
        ];

        foreach ($logs as $l) {
            if ($l['user_id']) {
                ActivityLog::create($l);
            }
        }
    }
}
