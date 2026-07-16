<?php

namespace Database\Seeders;

use App\Models\SuratTurunanTemplate;
use Illuminate\Database\Seeder;

class SuratTurunanTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [

            // ============================================================
            // 1. SURAT UNDANGAN
            // ============================================================
            [
                'kode'      => 'undangan',
                'nama'      => 'Surat Undangan',
                'is_active' => true,
                'konten_template' => <<<'TEMPLATE'
SURAT UNDANGAN
Nomor: {{nomor_surat_induk}}

Assalamu'alaikum Warahmatullahi Wabarakatuh,

Yang bertanda tangan di bawah ini, Pengurus {{organisasi_nama}}, dengan hormat mengundang Bapak/Ibu/Saudara/i untuk hadir dalam kegiatan:

Nama Kegiatan  : {{nama_kegiatan}}
Hari/Tanggal   : {{tanggal_mulai}}{{tanggal_selesai}}
Tempat         : {{lokasi}}
Keterangan     : {{deskripsi_singkat}}

Mengingat pentingnya kegiatan ini, besar harapan kami agar Bapak/Ibu/Saudara/i dapat hadir tepat waktu. Atas perhatian dan kehadiran Bapak/Ibu/Saudara/i, kami ucapkan terima kasih.

Wassalamu'alaikum Warahmatullahi Wabarakatuh,

{{tanggal_surat}}

Hormat kami,
{{organisasi_nama}}



_______________________          _______________________          _______________________
Ketua Pelaksana                  Pembina                          Kepala Sekolah
TEMPLATE
            ],

            // ============================================================
            // 2. SURAT IZIN KEGIATAN
            // ============================================================
            [
                'kode'      => 'izin_kegiatan',
                'nama'      => 'Surat Izin Kegiatan',
                'is_active' => true,
                'konten_template' => <<<'TEMPLATE'
SURAT IZIN KEGIATAN
Nomor: {{nomor_surat_induk}}

Yang bertanda tangan di bawah ini:

Nama Organisasi : {{organisasi_nama}}

Dengan ini mengajukan permohonan izin penyelenggaraan kegiatan kepada pihak yang berwenang, dengan rincian sebagai berikut:

Nama Kegiatan  : {{nama_kegiatan}}
Tanggal        : {{tanggal_mulai}}{{tanggal_selesai}}
Tempat         : {{lokasi}}
Keterangan     : {{deskripsi_singkat}}

Kegiatan ini merupakan program kerja {{organisasi_nama}} yang telah direncanakan dan mendapat persetujuan dari pihak terkait sebagaimana tercantum dalam dokumen dengan nomor surat {{nomor_surat_induk}}.

Demikian surat izin kegiatan ini kami ajukan. Atas perkenan dan izin yang diberikan, kami mengucapkan terima kasih.

{{tanggal_surat}}

Hormat kami,
{{organisasi_nama}}



_______________________          _______________________          _______________________
Ketua Pelaksana                  Pembina                          Kepala Sekolah
TEMPLATE
            ],

            // ============================================================
            // 3. SURAT PEMINJAMAN TEMPAT
            // ============================================================
            [
                'kode'      => 'peminjaman_tempat',
                'nama'      => 'Surat Peminjaman Tempat',
                'is_active' => true,
                'konten_template' => <<<'TEMPLATE'
SURAT PERMOHONAN PEMINJAMAN TEMPAT
Nomor: {{nomor_surat_induk}}

Kepada Yth.
Penanggung Jawab Sarana dan Prasarana
di Tempat

Assalamu'alaikum Warahmatullahi Wabarakatuh,

Dengan hormat, yang bertanda tangan di bawah ini atas nama {{organisasi_nama}}, mengajukan permohonan peminjaman tempat/ruangan guna keperluan penyelenggaraan kegiatan:

Nama Kegiatan  : {{nama_kegiatan}}
Tanggal        : {{tanggal_mulai}}{{tanggal_selesai}}
Tempat/Ruang   : {{lokasi}}
Keterangan     : {{deskripsi_singkat}}

Kami berkomitmen untuk menjaga kebersihan, ketertiban, dan kondisi tempat yang dipinjam, serta mengembalikannya dalam keadaan semula setelah kegiatan selesai.

Demikian permohonan ini kami sampaikan. Atas perhatian dan persetujuan Bapak/Ibu, kami ucapkan terima kasih.

Wassalamu'alaikum Warahmatullahi Wabarakatuh,

{{tanggal_surat}}

Hormat kami,
{{organisasi_nama}}



_______________________          _______________________          _______________________
Ketua Pelaksana                  Pembina                          Kepala Sekolah
TEMPLATE
            ],

            // ============================================================
            // 4. SURAT SPONSORSHIP
            // ============================================================
            [
                'kode'      => 'sponsorship',
                'nama'      => 'Surat Sponsorship',
                'is_active' => true,
                'konten_template' => <<<'TEMPLATE'
SURAT PERMOHONAN SPONSORSHIP
Nomor: {{nomor_surat_induk}}

Kepada Yth.
Pimpinan/Penanggung Jawab
[Nama Instansi/Perusahaan]
di Tempat

Assalamu'alaikum Warahmatullahi Wabarakatuh,

Dengan hormat, kami dari {{organisasi_nama}} bermaksud mengajukan permohonan dukungan sponsorship untuk kegiatan:

Nama Kegiatan  : {{nama_kegiatan}}
Tanggal        : {{tanggal_mulai}}{{tanggal_selesai}}
Tempat         : {{lokasi}}
Keterangan     : {{deskripsi_singkat}}

Kegiatan ini merupakan salah satu program kerja {{organisasi_nama}} yang bertujuan untuk meningkatkan potensi dan kreativitas peserta didik. Dukungan dari pihak Bapak/Ibu sangat berarti bagi terlaksananya kegiatan ini secara optimal.

Sebagai bentuk apresiasi, nama/logo instansi Bapak/Ibu akan dicantumkan dalam seluruh media publikasi kegiatan. Detail bentuk kerjasama dapat didiskusikan lebih lanjut sesuai kesepakatan bersama.

Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.

Wassalamu'alaikum Warahmatullahi Wabarakatuh,

{{tanggal_surat}}

Hormat kami,
{{organisasi_nama}}



_______________________          _______________________          _______________________
Ketua Pelaksana                  Pembina                          Kepala Sekolah
TEMPLATE
            ],
        ];

        foreach ($templates as $data) {
            SuratTurunanTemplate::updateOrCreate(
                ['kode' => $data['kode']],
                [
                    'nama'             => $data['nama'],
                    'konten_template'  => $data['konten_template'],
                    'is_active'        => $data['is_active'],
                ]
            );
        }
    }
}
