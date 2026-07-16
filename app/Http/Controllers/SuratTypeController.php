<?php

namespace App\Http\Controllers;

use App\Models\SuratType;
use App\Models\SuratTypeApprover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuratTypeController extends Controller
{
    public function index()
    {
        $suratTypes = SuratType::with(['organisasi'])->withCount(['surats', 'approvers'])->get();
        $organisasis = \App\Models\Organisasi::where('is_active', true)->get();
        return view('surat-type.index', compact('suratTypes', 'organisasis'));
    }

    public function create()
    {
        $approverUsers = \App\Models\User::with(['profile', 'roles'])
            ->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->whereIn('name', ['admin', 'super-admin']);
            })
            ->where(function($q) {
                // Include: all hr and supervisor
                $q->whereHas('roles', function($roleQuery) {
                    $roleQuery->whereIn('name', ['hr', 'supervisor']);
                })
                // Include: anyone (including staff) who has jabatan set
                ->orWhereHas('profile', function($profileQuery) {
                    $profileQuery->whereNotNull('jabatan_struktural')
                                 ->where('jabatan_struktural', '!=', '');
                });
            })
            ->get()
            ->map(function($user) {
                return [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'role'   => $user->roles->first()?->name ?? 'staff',
                    'jabatan'=> $user->profile?->jabatan ?? '-',
                ];
            });

        $organisasis = \App\Models\Organisasi::where('is_active', true)->get();
        return view('surat-type.create', compact('approverUsers', 'organisasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:surat_types,kode',
            'nomor_format' => 'required|array',
            'organisasi_id' => 'nullable|exists:organisasis,id',
            'approvers' => 'required|array|min:1',
            'approvers.*.jabatan_label' => 'required|string|max:100',
            'approvers.*.target_mode' => 'required|in:submitter,fixed_osis,fixed_mpk,global',
            'approvers.*.user_id' => 'nullable|exists:users,id',
            'approvers.*.is_signer' => 'nullable',
            'approvers.*.metode_ttd' => 'nullable|in:stamp,append',
        ]);

        $organisasiTipe = null;
        if ($request->organisasi_id) {
            $org = \App\Models\Organisasi::find($request->organisasi_id);
            if ($org) {
                $organisasiTipe = $org->tipe;
            }
        }

        DB::transaction(function () use ($request, $organisasiTipe) {
            $suratType = SuratType::create([
                'nama' => $request->nama,
                'kode' => Str::slug($request->kode),
                'deskripsi' => $request->deskripsi,
                'organisasi_tipe' => $organisasiTipe,
                'organisasi_id' => $request->organisasi_id,
                'nomor_format' => $request->nomor_format,
                'nomor_reset' => $request->nomor_reset ?? 'yearly',
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
            ]);

            foreach ($request->approvers as $index => $approver) {
                $isSigner = isset($approver['is_signer']);
                $suratType->approvers()->create([
                    'urutan' => $index + 1,
                    'user_id' => $approver['user_id'] ?? null,
                    'target_mode' => $approver['target_mode'] ?? 'submitter',
                    'jabatan_label' => $approver['jabatan_label'] ?? '-',
                    'is_signer' => $isSigner,
                    'metode_ttd' => $isSigner ? ($approver['metode_ttd'] ?? 'stamp') : null,
                    'is_required' => isset($approver['is_required']),
                ]);
            }
        });

        flash()->success('Document type created successfully.');
        return redirect()->route('surat-type.index');
    }

    public function edit($id)
    {
        $suratType = SuratType::with('approvers')->findOrFail($id);
        
        $approverUsers = \App\Models\User::with(['profile', 'roles'])
            ->whereDoesntHave('roles', function($roleQuery) {
                $roleQuery->whereIn('name', ['admin', 'super-admin']);
            })
            ->where(function($q) {
                $q->whereHas('roles', function($roleQuery) {
                    $roleQuery->whereIn('name', ['hr', 'supervisor']);
                })
                ->orWhereHas('profile', function($profileQuery) {
                    $profileQuery->whereNotNull('jabatan_struktural')
                                 ->where('jabatan_struktural', '!=', '');
                });
            })
            ->get()
            ->map(function($user) {
                return [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'role'   => $user->roles->first()?->name ?? 'staff',
                    'jabatan'=> $user->profile?->jabatan ?? '-',
                ];
            });

        $organisasis = \App\Models\Organisasi::where('is_active', true)->get();
        return view('surat-type.create', compact('suratType', 'approverUsers', 'organisasis'));
    }

    public function update(Request $request, $id)
    {
        $suratType = SuratType::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:surat_types,kode,' . $id,
            'nomor_format' => 'required|array',
            'organisasi_id' => 'nullable|exists:organisasis,id',
            'approvers' => 'required|array|min:1',
            'approvers.*.jabatan_label' => 'required|string|max:100',
            'approvers.*.target_mode' => 'required|in:submitter,fixed_osis,fixed_mpk,global',
            'approvers.*.user_id' => 'nullable|exists:users,id',
            'approvers.*.is_signer' => 'nullable',
            'approvers.*.metode_ttd' => 'nullable|in:stamp,append',
        ]);

        $organisasiTipe = null;
        if ($request->organisasi_id) {
            $org = \App\Models\Organisasi::find($request->organisasi_id);
            if ($org) {
                $organisasiTipe = $org->tipe;
            }
        }

        DB::transaction(function () use ($request, $suratType, $organisasiTipe) {
            $suratType->update([
                'nama' => $request->nama,
                'kode' => Str::slug($request->kode),
                'deskripsi' => $request->deskripsi,
                'organisasi_tipe' => $organisasiTipe,
                'organisasi_id' => $request->organisasi_id,
                'nomor_format' => $request->nomor_format,
                'nomor_reset' => $request->nomor_reset ?? 'yearly',
                'is_active' => $request->boolean('is_active'),
            ]);

            $suratType->approvers()->delete();
            foreach ($request->approvers as $index => $approver) {
                $isSigner = isset($approver['is_signer']);
                $suratType->approvers()->create([
                    'urutan' => $index + 1,
                    'user_id' => $approver['user_id'] ?? null,
                    'target_mode' => $approver['target_mode'] ?? 'submitter',
                    'jabatan_label' => $approver['jabatan_label'] ?? '-',
                    'is_signer' => $isSigner,
                    'metode_ttd' => $isSigner ? ($approver['metode_ttd'] ?? 'stamp') : null,
                    'is_required' => isset($approver['is_required']),
                ]);
            }
        });

        flash()->success('Document type updated successfully.');
        return redirect()->route('surat-type.index');
    }

    public function destroy($id)
    {
        $suratType = SuratType::findOrFail($id);

        DB::transaction(function () use ($suratType) {
            // delete associated documents and their approvals
            $surats = $suratType->surats;
            foreach ($surats as $surat) {
                // delete approvals first
                $surat->approvals()->delete();
                
                // delete files from storage if they exist
                if ($surat->file_pdf) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($surat->file_pdf);
                }
                if ($surat->cover_pdf_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($surat->cover_pdf_path);
                }
                if ($surat->final_pdf_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($surat->final_pdf_path);
                }
                
                $surat->delete();
            }

            // delete approver configurations
            $suratType->approvers()->delete();
            
            // finally delete the document type
            $suratType->delete();
        });

        flash()->success('Document type and all associated records deleted successfully.');
        return redirect()->route('surat-type.index');
    }

    public function toggle($id)
    {
        $suratType = SuratType::findOrFail($id);
        $suratType->update(['is_active' => !$suratType->is_active]);

        return response()->json(['success' => true, 'is_active' => $suratType->is_active]);
    }
}
