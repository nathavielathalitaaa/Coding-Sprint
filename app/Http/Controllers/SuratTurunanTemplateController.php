<?php

namespace App\Http\Controllers;

use App\Models\SuratTurunanTemplate;
use Illuminate\Http\Request;

class SuratTurunanTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|super-admin');
    }

    /**
     * Daftar placeholder token yang didukung — sumber kebenaran tunggal,
     * dipakai di view agar tidak hardcode di dua tempat.
     */
    private function placeholders(): array
    {
        return [
            ['token' => '{{nama_kegiatan}}',     'keterangan' => 'Nama kegiatan dari detail surat'],
            ['token' => '{{tanggal_mulai}}',      'keterangan' => 'Tanggal mulai (format: dd MMMM yyyy)'],
            ['token' => '{{tanggal_selesai}}',    'keterangan' => 'Tanggal selesai (kosong jika tidak diisi, muncul dengan prefix " s.d. ")'],
            ['token' => '{{lokasi}}',             'keterangan' => 'Lokasi pelaksanaan kegiatan'],
            ['token' => '{{deskripsi_singkat}}',  'keterangan' => 'Deskripsi singkat kegiatan (default: "-" jika kosong)'],
            ['token' => '{{organisasi_nama}}',    'keterangan' => 'Nama organisasi pengaju surat'],
            ['token' => '{{nomor_surat_induk}}',  'keterangan' => 'Nomor surat induk yang sudah disetujui'],
            ['token' => '{{tanggal_surat}}',      'keterangan' => 'Tanggal generate surat (hari ini, format: dd MMMM yyyy)'],
        ];
    }

    // ── INDEX ──────────────────────────────────────────────────────────────

    public function index()
    {
        $templates = SuratTurunanTemplate::orderBy('kode')->get();
        return view('surat-turunan-template.index', compact('templates'));
    }

    // ── EDIT ───────────────────────────────────────────────────────────────

    public function edit(SuratTurunanTemplate $suratTurunanTemplate)
    {
        return view('surat-turunan-template.edit', [
            'template'     => $suratTurunanTemplate,
            'placeholders' => $this->placeholders(),
        ]);
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────

    public function update(Request $request, SuratTurunanTemplate $suratTurunanTemplate)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'konten_template'  => 'required|string',
            'is_active'        => 'nullable|boolean',
        ], [
            'nama.required'            => 'Nama template wajib diisi.',
            'konten_template.required' => 'Konten template tidak boleh kosong.',
        ]);

        $suratTurunanTemplate->update([
            'nama'            => $request->nama,
            'konten_template' => $request->konten_template,
            'is_active'       => $request->boolean('is_active'),
        ]);

        flash()->success("Template '{$suratTurunanTemplate->nama}' berhasil diperbarui.");
        return redirect()->route('surat-turunan-template.index');
    }

    // ── TOGGLE ACTIVE ──────────────────────────────────────────────────────

    public function toggle(SuratTurunanTemplate $suratTurunanTemplate)
    {
        $suratTurunanTemplate->update(['is_active' => !$suratTurunanTemplate->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $suratTurunanTemplate->is_active,
        ]);
    }
}
