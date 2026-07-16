<?php

namespace Database\Seeders;

use App\Models\Komisi;
use App\Models\KomisiMember;
use App\Models\Organisasi;
use App\Models\OrganisasiMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        // ── Buat Organisasi Inti ────────────────────────────────────────
        $osis = Organisasi::create([
            'nama'       => 'OSIS SMK Telkom Sidoarjo',
            'tipe'       => 'osis',
            'deskripsi'  => 'Organisasi Siswa Intra Sekolah SMK Telkom Sidoarjo',
            'is_active'  => true,
        ]);

        $mpk = Organisasi::create([
            'nama'       => 'MPK SMK Telkom Sidoarjo',
            'tipe'       => 'mpk',
            'deskripsi'  => 'Majelis Permusyawaratan Kelas SMK Telkom Sidoarjo',
            'is_active'  => true,
        ]);

        $rohis = Organisasi::create([
            'nama'       => 'ROHIS SMK Telkom Sidoarjo',
            'tipe'       => 'sub_organ',
            'deskripsi'  => 'Rohani Islam SMK Telkom Sidoarjo',
            'is_active'  => true,
        ]);

        // ── Ambil User Demo ─────────────────────────────────────────────
        $ketuaOsis   = User::where('user_id', 'ANT-001')->first();
        $bphOsis1    = User::where('user_id', 'ANT-002')->first();
        $ketuaMpk    = User::where('user_id', 'ANT-003')->first();
        $bphMpk1     = User::where('user_id', 'ANT-004')->first();
        $anggotaRohis= User::where('user_id', 'ANT-005')->first();
        $pembinOsis  = User::where('user_id', 'GURU-003')->first();
        $pembinaMpk  = User::where('user_id', 'GURU-004')->first();

        // ── Assign Anggota OSIS ─────────────────────────────────────────
        if ($ketuaOsis) {
            OrganisasiMember::create(['user_id' => $ketuaOsis->id, 'organisasi_id' => $osis->id, 'jabatan' => 'ketua']);
        }
        if ($bphOsis1) {
            OrganisasiMember::create(['user_id' => $bphOsis1->id,  'organisasi_id' => $osis->id, 'jabatan' => 'bph']);
        }
        if ($pembinOsis) {
            OrganisasiMember::create(['user_id' => $pembinOsis->id, 'organisasi_id' => $osis->id, 'jabatan' => 'pembina']);
        }

        // ── Assign Anggota MPK ──────────────────────────────────────────
        if ($ketuaMpk) {
            OrganisasiMember::create(['user_id' => $ketuaMpk->id,  'organisasi_id' => $mpk->id, 'jabatan' => 'ketua']);
        }
        if ($bphMpk1) {
            OrganisasiMember::create(['user_id' => $bphMpk1->id,   'organisasi_id' => $mpk->id, 'jabatan' => 'bph']);
        }
        if ($pembinaMpk) {
            OrganisasiMember::create(['user_id' => $pembinaMpk->id, 'organisasi_id' => $mpk->id, 'jabatan' => 'pembina']);
        }

        // ── Buat Komisi MPK & Assign Anggota ───────────────────────────
        $komisiReligiMoral = Komisi::create([
            'nama'         => 'Komisi Religi & Moral',
            'organisasi_id'=> $mpk->id,
            'deskripsi'    => 'Komisi yang menangani urusan religi dan moral siswa',
            'is_active'    => true,
        ]);

        // ── Assign Anggota ROHIS (Sub Organ) ───────────────────────────
        if ($anggotaRohis) {
            OrganisasiMember::create(['user_id' => $anggotaRohis->id, 'organisasi_id' => $rohis->id, 'jabatan' => 'bph']);
        }
        if ($bphOsis1) {
            // BPH OSIS jadi Pengawas di Sub Organ ROHIS
            OrganisasiMember::create(['user_id' => $bphOsis1->id, 'organisasi_id' => $rohis->id, 'jabatan' => 'pengawas']);
        }
        if ($pembinOsis) {
            OrganisasiMember::create(['user_id' => $pembinOsis->id, 'organisasi_id' => $rohis->id, 'jabatan' => 'pembina']);
        }

        // Assign user ke Komisi MPK
        if ($anggotaRohis) {
            KomisiMember::create(['user_id' => $anggotaRohis->id, 'komisi_id' => $komisiReligiMoral->id]);
        }
    }
}
