<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTypeApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_type_id',
        'user_id',
        'urutan',
        'jabatan_label',
        'target_mode',    // submitter | fixed_osis | fixed_mpk | global
        'label',
        'metode_ttd',
        'is_signer',
        'is_required',
    ];

    protected $casts = [
        'is_signer'   => 'boolean',
        'is_required' => 'boolean',
    ];

    // ── target_mode options ────────────────────────────
    public static function targetModeOptions(): array
    {
        return [
            'submitter'  => 'Submitter (sama organisasi dengan pengaju)',
            'fixed_osis' => 'Fixed OSIS (BPH OSIS manapun)',
            'fixed_mpk'  => 'Fixed MPK (BPH MPK manapun)',
            'global'     => 'Global (role Pengawas Pusat / Kepala Sekolah)',
        ];
    }

    public function suratType()
    {
        return $this->belongsTo(SuratType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
