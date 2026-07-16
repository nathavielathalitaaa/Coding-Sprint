<?php

namespace App\Http\Requests\Surat;

use App\Models\SuratType;
use Illuminate\Foundation\Http\FormRequest;

class StoreSuratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Cek apakah jenis surat yang dipilih memerlukan detail kegiatan
        $requiresDetail = false;
        if ($this->filled('surat_type_id')) {
            $suratType = SuratType::find($this->surat_type_id);
            $requiresDetail = (bool) $suratType?->requires_kegiatan_detail;
        }

        $required = $requiresDetail ? 'required' : 'nullable';

        return [
            'surat_type_id'    => 'required|exists:surat_types,id',
            'organisasi_id'    => 'required|exists:organisasis,id',
            'jenis_surat'      => 'nullable|string',
            'perihal'          => 'required|string',
            'file_pdf'         => 'required|file|mimes:pdf|max:5120',
            'ttd_coordinates'  => 'nullable|string',

            // ── Detail Kegiatan (kondisional) ──────────────────────────
            'nama_kegiatan'    => "{$required}|string|max:255",
            'tanggal_mulai'    => "{$required}|date",
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi'           => "{$required}|string|max:255",
            'deskripsi_singkat'=> 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'jenis_surat.required'        => 'Jenis surat wajib diisi',
            'perihal.required'            => 'Perihal wajib diisi',
            'file_pdf.required'           => 'File surat wajib diunggah.',
            'file_pdf.mimes'              => 'File harus berformat PDF',
            'file_pdf.max'                => 'Ukuran file maksimal 5MB',

            // Detail kegiatan
            'nama_kegiatan.required'      => 'Nama kegiatan wajib diisi untuk jenis surat ini.',
            'tanggal_mulai.required'      => 'Tanggal mulai kegiatan wajib diisi.',
            'tanggal_mulai.date'          => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.date'        => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'lokasi.required'             => 'Lokasi kegiatan wajib diisi.',
        ];
    }
}
