<?php

namespace App\Http\Controllers;

use App\Models\ApprovalStep;
use App\Models\DocumentApproval;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\Request;

class ApprovalFlowController extends Controller
{
    // daftar jenis surat yang valid
    private array $jenisOptions = [
        'surat_izin'        => 'Surat Izin',
        'surat_permohonan'  => 'Surat Permohonan',
        'surat_resign'      => 'Surat Resign',
        'surat_surat_tugas' => 'Surat Tugas',
        'surat_rekomendasi' => 'Surat Rekomendasi',
        'surat_lainnya'     => 'Surat Lainnya',
    ];

    // daftar jabatan approver yang tersedia
    private array $jabatanOptions = [
        'hod'       => 'Head of Department',
        'hr'        => 'Human Resources',
        'purchasing'=> 'Purchasing',
        'owner_rep' => 'Owner Representative',
        'direktur'  => 'Direktur',
    ];

    /**
     * halaman utama — tampilkan semua flow per jenis surat
     */
    public function index()
    {
        $flows = [];
        foreach ($this->jenisOptions as $type => $label) {
            $flows[$type] = [
                'label' => $label,
                'steps' => ApprovalStep::where('document_type', $type)
                    ->orderBy('step_order')->get(),
            ];
        }

        return view('hr.approval-flow.index', compact('flows'));
    }

    /**
     * form edit flow untuk satu jenis surat
     */
    public function edit(string $type)
    {
        abort_unless(array_key_exists($type, $this->jenisOptions), 404);

        $steps   = ApprovalStep::where('document_type', $type)->orderBy('step_order')->get();
        $label   = $this->jenisOptions[$type];
        $jabatanOptions = $this->jabatanOptions;

        // ambil pengaturan ttd dari step pertama (sebagai representasi jenis surat ini)
        $firstStep = $steps->first();
        $ttd_mode = $firstStep->ttd_mode ?? 'append';
        $setting_overrides = $firstStep->setting_overrides ?? null;

        return view('hr.approval-flow.edit', compact('type', 'label', 'steps', 'jabatanOptions', 'ttd_mode', 'setting_overrides'));
    }

    /**
     * simpan perubahan flow — replace semua step untuk jenis surat ini
     */
    public function update(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, $this->jenisOptions), 404);

        $request->validate([
            'steps'             => 'required|array|min:1|max:5',
            'steps.*.jabatan'   => 'required|string|in:hod,hr,purchasing,owner_rep,direktur',
            'steps.*.label'     => 'required|string|max:100',
            'ttd_mode'          => 'required|in:stamp,append',
            'use_override'      => 'nullable|boolean',
            'override.company_name' => 'required_if:use_override,1|nullable|string|max:255',
            'override.accent_color' => 'required_if:use_override,1|nullable|string|size:7',
            'override.font_family'  => 'required_if:use_override,1|nullable|string|in:Arial,Times New Roman,Helvetica,Georgia',
            'override.footer_text'  => 'nullable|string|max:500',
            'override_logo'         => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // siapkan setting_overrides
        $setting_overrides = null;
        if ($request->use_override) {
            $setting_overrides = $request->override;
            
            // handle logo upload for override
            if ($request->hasFile('override_logo')) {
                $path = $request->file('override_logo')->store('document-logos', 'public');
                $setting_overrides['logo_path'] = $path;
            } else {
                // keep existing logo path if already set in previous steps
                $oldFirstStep = ApprovalStep::where('document_type', $type)->first();
                if ($oldFirstStep && isset($oldFirstStep->setting_overrides['logo_path'])) {
                    $setting_overrides['logo_path'] = $oldFirstStep->setting_overrides['logo_path'];
                }
            }
        }

        // hapus semua step lama untuk jenis surat ini
        ApprovalStep::where('document_type', $type)->delete();

        // simpan step baru
        foreach ($request->steps as $order => $step) {
            ApprovalStep::create([
                'document_type'     => $type,
                'step_order'        => $order + 1,
                'jabatan'           => $step['jabatan'],
                'label'             => $step['label'],
                'ttd_mode'          => $request->ttd_mode,
                'setting_overrides' => $setting_overrides,
            ]);
        }

        flash()->success('Flow approval ' . $this->jenisOptions[$type] . ' berhasil diperbarui.');
        return redirect()->route('hr.approval-flow.index');
    }

    /**
     * halaman reassign — daftar surat yang sedang pending/waiting
     */
    public function reassignIndex()
    {
        // surat yang masih dalam proses (ada step waiting)
        $surats = Surat::with(['user', 'approvals'])
            ->whereHas('approvals', fn($q) => $q->where('status', 'waiting'))
            ->where('status', 'submitted')
            ->latest()->get();

        // daftar user yang punya jabatan approver (untuk dropdown pengganti)
        $approvers = User::whereHas('profile', fn($q) =>
            $q->whereIn('jabatan', array_keys($this->jabatanOptions))
              ->where(fn($q2) => $q2->whereNotNull('signature_path')->orWhereNotNull('ttd_path'))
              ->whereNotNull('pin')
        )->with('profile')->get();

        $jabatanOptions = $this->jabatanOptions;

        return view('hr.approval-flow.reassign', compact('surats', 'approvers', 'jabatanOptions'));
    }

    /**
     * terapkan reassign — ganti jabatan di step yang sedang waiting
     */
    public function reassignApply(Request $request)
    {
        $request->validate([
            'approval_id'   => 'required|exists:document_approvals,id',
            'jabatan_baru'  => 'required|in:hod,hr,purchasing,owner_rep,direktur',
        ]);

        $step = DocumentApproval::findOrFail($request->approval_id);

        // hanya bisa reassign step yang sedang waiting
        if ($step->status !== 'waiting') {
            flash()->error('Hanya step yang sedang menunggu yang bisa di-reassign.');
            return redirect()->back();
        }

        $jabatanLama = $step->jabatan;
        $step->update([
            'jabatan' => $request->jabatan_baru,
            'label'   => $this->jabatanOptions[$request->jabatan_baru],
        ]);

        flash()->success("Step approval berhasil dialihkan dari {$this->jabatanOptions[$jabatanLama]} ke {$this->jabatanOptions[$request->jabatan_baru]}.");
        return redirect()->route('hr.approval-flow.reassign');
    }
}
