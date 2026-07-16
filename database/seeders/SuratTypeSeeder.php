<?php

namespace Database\Seeders;

use App\Models\SuratType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratTypeSeeder extends Seeder
{
    public function run()
    {
        $adminId = User::where('role_name', 'admin')->orWhere('role_name', 'super-admin')->first()?->id ?? 1;

        $types = [
            // ========================================================
            // OSIS
            // ========================================================
            [
                'nama'                    => 'Proposal Kegiatan (OSIS)',
                'kode'                    => 'proposal_osis',
                'organisasi_tipe'         => 'osis',
                'requires_kegiatan_detail'=> true,
                'deskripsi'               => 'Pengajuan proposal kegiatan oleh OSIS.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'OSIS'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph_osis',      'target_mode' => 'submitter',  'label' => 'Diajukan BPH OSIS',        'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'fixed_mpk',  'label' => 'Disetujui BPH MPK',        'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina OSIS',   'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 4, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat', 'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 5, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah', 'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
            [
                'nama'            => 'Surat Resmi (OSIS)',
                'kode'            => 'surat_resmi_osis',
                'organisasi_tipe' => 'osis',
                'requires_kegiatan_detail' => false,
                'deskripsi'       => 'Pengajuan surat resmi keluar oleh OSIS.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'OSIS'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph_osis',      'target_mode' => 'submitter',  'label' => 'Diajukan BPH OSIS',        'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'fixed_mpk',  'label' => 'Disetujui BPH MPK',        'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina OSIS',   'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 4, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat', 'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 5, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah', 'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
            [
                'nama'            => 'Administrasi Organisasi (OSIS)',
                'kode'            => 'administrasi_osis',
                'organisasi_tipe' => 'osis',
                'requires_kegiatan_detail' => false,
                'deskripsi'       => 'Pengajuan surat administrasi internal oleh OSIS.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'OSIS'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph_osis',      'target_mode' => 'submitter',  'label' => 'Diajukan BPH OSIS',        'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'fixed_mpk',  'label' => 'Disetujui BPH MPK',        'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina OSIS',   'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 4, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat', 'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 5, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah', 'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],

            // ========================================================
            // SUB ORGAN
            // ========================================================
            [
                'nama'                     => 'Proposal Kegiatan (Sub Organ)',
                'kode'                     => 'proposal_sub_organ',
                'organisasi_tipe'          => 'sub_organ',
                'requires_kegiatan_detail' => true,
                'deskripsi'                => 'Pengajuan proposal kegiatan oleh Sub Organisasi.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'SUB'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph',           'target_mode' => 'submitter',  'label' => 'Diajukan BPH Sub Organ',       'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'bph_osis',      'target_mode' => 'fixed_osis', 'label' => 'Disetujui BPH OSIS',           'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'fixed_mpk',  'label' => 'Disetujui BPH MPK',            'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 4, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina Sub Organ',   'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 5, 'jabatan_label' => 'pengawas',      'target_mode' => 'submitter',  'label' => 'Disetujui Pengawas Sub Organ',  'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 6, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat',      'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 7, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah',      'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
            [
                'nama'            => 'Surat Resmi (Sub Organ)',
                'kode'            => 'surat_resmi_sub_organ',
                'organisasi_tipe' => 'sub_organ',
                'requires_kegiatan_detail' => false,
                'deskripsi'       => 'Pengajuan surat resmi keluar oleh Sub Organisasi.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'SUB'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph',           'target_mode' => 'submitter',  'label' => 'Diajukan BPH Sub Organ',       'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'bph_osis',      'target_mode' => 'fixed_osis', 'label' => 'Disetujui BPH OSIS',           'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'fixed_mpk',  'label' => 'Disetujui BPH MPK',            'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 4, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina Sub Organ',   'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 5, 'jabatan_label' => 'pengawas',      'target_mode' => 'submitter',  'label' => 'Disetujui Pengawas Sub Organ',  'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 6, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat',      'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 7, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah',      'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
            [
                'nama'            => 'Administrasi Organisasi (Sub Organ)',
                'kode'            => 'administrasi_sub_organ',
                'organisasi_tipe' => 'sub_organ',
                'requires_kegiatan_detail' => false,
                'deskripsi'       => 'Pengajuan surat administrasi internal oleh Sub Organisasi.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'SUB'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph',           'target_mode' => 'submitter',  'label' => 'Diajukan BPH Sub Organ',       'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'bph_osis',      'target_mode' => 'fixed_osis', 'label' => 'Disetujui BPH OSIS',           'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'fixed_mpk',  'label' => 'Disetujui BPH MPK',            'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 4, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina Sub Organ',   'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 5, 'jabatan_label' => 'pengawas',      'target_mode' => 'submitter',  'label' => 'Disetujui Pengawas Sub Organ',  'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 6, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat',      'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 7, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah',      'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],

            // ========================================================
            // MPK
            // ========================================================
            [
                'nama'                     => 'Proposal Kegiatan (MPK)',
                'kode'                     => 'proposal_mpk',
                'organisasi_tipe'          => 'mpk',
                'requires_kegiatan_detail' => true,
                'deskripsi'                => 'Pengajuan proposal kegiatan oleh MPK.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'MPK'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'submitter',  'label' => 'Diajukan BPH MPK',         'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'divisi',        'target_mode' => 'submitter',  'label' => 'Disetujui Komisi MPK',     'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina MPK',    'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 4, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat', 'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 5, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah', 'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
            [
                'nama'            => 'Surat Resmi (MPK)',
                'kode'            => 'surat_resmi_mpk',
                'organisasi_tipe' => 'mpk',
                'requires_kegiatan_detail' => false,
                'deskripsi'       => 'Pengajuan surat resmi keluar oleh MPK.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'MPK'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'submitter',  'label' => 'Diajukan BPH MPK',         'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'divisi',        'target_mode' => 'submitter',  'label' => 'Disetujui Komisi MPK',     'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina MPK',    'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 4, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat', 'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 5, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah', 'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
            [
                'nama'            => 'Administrasi Organisasi (MPK)',
                'kode'            => 'administrasi_mpk',
                'organisasi_tipe' => 'mpk',
                'requires_kegiatan_detail' => false,
                'deskripsi'       => 'Pengajuan surat administrasi internal oleh MPK.',
                'nomor_format'    => [
                    ['type' => 'NOMOR_URUT'],
                    ['type' => 'KODE_SURAT'],
                    ['type' => 'LEMBAGA', 'value' => 'MPK'],
                    ['type' => 'BULAN_ROMAWI'],
                    ['type' => 'TAHUN'],
                ],
                'approvers'       => [
                    ['urutan' => 1, 'jabatan_label' => 'bph_mpk',       'target_mode' => 'submitter',  'label' => 'Diajukan BPH MPK',         'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 2, 'jabatan_label' => 'divisi',        'target_mode' => 'submitter',  'label' => 'Disetujui Komisi MPK',     'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 3, 'jabatan_label' => 'pembina',       'target_mode' => 'submitter',  'label' => 'Disetujui Pembina MPK',    'metode_ttd' => 'stamp', 'is_signer' => true],
                    ['urutan' => 4, 'jabatan_label' => 'pengawas_pusat','target_mode' => 'global',     'label' => 'Disetujui Pengawas Pusat', 'metode_ttd' => 'stamp', 'is_signer' => false],
                    ['urutan' => 5, 'jabatan_label' => 'kepala_sekolah','target_mode' => 'global',     'label' => 'Disetujui Kepala Sekolah', 'metode_ttd' => 'stamp', 'is_signer' => true],
                ],
            ],
        ];

        DB::transaction(function () use ($types, $adminId) {
            foreach ($types as $t) {
                $suratType = SuratType::updateOrCreate(
                    ['kode' => $t['kode']],
                    [
                        'nama'                     => $t['nama'],
                        'organisasi_tipe'          => $t['organisasi_tipe'],
                        'requires_kegiatan_detail' => $t['requires_kegiatan_detail'] ?? false,
                        'deskripsi'                => $t['deskripsi'],
                        'nomor_format'             => $t['nomor_format'],
                        'created_by'               => $adminId,
                        'nomor_reset'              => 'yearly',
                        'is_active'                => true,
                    ]
                );

                $suratType->approvers()->delete();
                foreach ($t['approvers'] as $app) {
                    $suratType->approvers()->create($app);
                }
            }
        });
    }
}
