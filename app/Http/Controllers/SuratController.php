<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Services\ApprovalService;
use App\Services\PinVerificationService;
use App\Services\ApprovalCoverService;
use App\Http\Requests\Surat\StoreSuratRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    public function __construct(
        private ApprovalService $approval,
        private PinVerificationService $pinService,
        private ApprovalCoverService $coverService,
        private \App\Services\PdfStampService $stampService,
        private \App\Services\NotificationService $notifService,
    ) {
        $this->middleware('auth');
    }

    // ── index ───────────────────────────────────────────────
    /** tampilkan daftar surat */
    public function index()
    {
        $user    = Auth::user()->load('profile');
        $jabatan = $user->profile?->jabatan;

        $query = Surat::with(['user', 'approvals']);

        if ($jabatan) {
            $query->where(function($q) use ($jabatan, $user) {
                $q->whereHas('approvals', function ($sq) use ($jabatan) {
                    $sq->where('jabatan', $jabatan)
                      ->whereIn('status', ['waiting', 'approved', 'rejected', 'pending'])
                      ->where('document_type', 'LIKE', 'surat_%');
                })
                ->orWhere('user_id', $user->id);
            });
        } elseif ($user->hasRole('staff')) {
            $query->where('user_id', $user->id);
        }

        $surats = $query->latest()->paginate(15);

        return view('surat.index', compact('surats'));
    }

    // ── create ──────────────────────────────────────────────
    /** tampilkan form buat surat */
    public function create()
    {
        $this->authorize('create', Surat::class);
        return view('surat.create');
    }

    // ── store ───────────────────────────────────────────────
    /** simpan surat baru */
    public function store(StoreSuratRequest $request)
    {
        $this->authorize('store', Surat::class);

        $fileName = null;
        if ($request->hasFile('file_pdf')) {
            $fileName = $request->file('file_pdf')->store('surat', 'public');
        }

        $surat = Surat::create([
            'user_id'         => Auth::id(),
            'nomor_surat'     => $this->generateNomorSurat(),
            'jenis_surat'     => $request->validated()['jenis_surat'],
            'perihal'         => $request->validated()['perihal'],
            'file_pdf'        => $fileName,
            'ttd_coordinates' => $request->ttd_coordinates ? json_decode($request->ttd_coordinates, true) : null,
            'status'          => 'submitted',
        ]);

        // init approval 4 step
        $this->approval->initApproval('surat_' . $surat->jenis_surat, $surat->id);

        // notify first approver
        $firstStep = $surat->approvals()->where('status', 'waiting')->first();
        if ($firstStep) {
            $this->notifService->sendToJabatan(
                $firstStep->jabatan,
                'Permintaan Approval Surat Baru',
                "Karyawan " . Auth::user()->name . " mengajukan surat baru ({$surat->nomor_surat}).",
                route('surat.show', $surat->id)
            );
        }

        flash()->success('Surat berhasil dibuat dan dikirim untuk approval.');
        return redirect()->route('surat.show', $surat->id);
    }

    // ── show ────────────────────────────────────────────────
    /** tampilkan detail surat */
    public function show(Surat $surat)
    {
        $this->authorize('view', $surat);

        $documentType = 'surat_' . $surat->jenis_surat;
        $steps      = $this->approval->getStatus($documentType, $surat->id);
        $authUser   = Auth::user()->load('profile');
        $canApprove = $this->approval->canApprove($documentType, $surat->id, $authUser);
        $waitingStep = $this->approval->getWaitingStep($documentType, $surat->id);
        
        // tandai sudah dibaca jika ini giliran user ini
        $this->approval->markAsRead($documentType, $surat->id, $authUser);

        return view('surat.show', compact('surat', 'steps', 'canApprove', 'waitingStep'));
    }

    // ── edit ────────────────────────────────────────────────
    /** tampilkan form edit surat */
    public function edit(Surat $surat)
    {
        $this->authorize('edit', $surat);

        if (!$surat->canBeEdited()) {
            flash()->error('Surat sudah dalam proses approval dan tidak dapat diubah.');
            return redirect()->route('surat.show', $surat->id);
        }

        return view('surat.edit', compact('surat'));
    }

    // ── update (resubmit setelah revisi) ────────────────────
    /** update data surat */
    public function update(StoreSuratRequest $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        if (!$surat->canBeEdited()) {
            flash()->error('Surat sudah dalam proses approval dan tidak dapat diubah.');
            return redirect()->route('surat.show', $surat->id);
        }

        if ($request->hasFile('file_pdf')) {
            if ($surat->file_pdf && file_exists(storage_path('app/public/' . $surat->file_pdf))) {
                unlink(storage_path('app/public/' . $surat->file_pdf));
            }
            $surat->update(['file_pdf' => $request->file('file_pdf')->store('surat', 'public')]);
        }

        $surat->update([
            'status'        => 'submitted',
            'catatan_revisi' => null,
        ]);

        // reset approval dari step 1
        $this->approval->resubmit('surat_' . $surat->jenis_surat, $surat->id);

        flash()->success('Surat berhasil direvisi dan dikirim ulang untuk approval.');
        return redirect()->route('surat.show', $surat->id);
    }

    // ── approve ─────────────────────────────────────────────
    /** setujui surat */
    public function approve(Request $request, Surat $surat)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
            'pin'     => 'required|string',
        ], [
            'pin.required' => 'PIN wajib diisi untuk menyetujui surat.',
        ]);

        $user = Auth::user()->load('profile');

        // verifikasi pin
        if (!$this->pinService->verify($user, $request->pin)) {
            flash()->error('PIN salah. Silakan coba lagi.');
            return redirect()->back();
        }

        // server-side guard: cek apakah giliran jabatan user ini
        $documentType = 'surat_' . $surat->jenis_surat;
        if (!$this->approval->canApprove($documentType, $surat->id, $user)) {
            flash()->error('Bukan giliran Anda untuk approve surat ini.');
            return redirect()->back();
        }

        // ambil path ttd untuk snapshot
        $ttdSnapshot = $this->pinService->getTtdPath($user);

        $result = $this->approval->approve(
            $documentType,
            $surat->id,
            $user,
            $request->catatan ?? '',
            $ttdSnapshot
        );

        \Log::info('Approve result', $result);
        \Log::info('Cover PDF path', ['cover_pdf_path' => $surat->cover_pdf_path]);

        if (!$result['success']) {
            flash()->error($result['message']);
            return redirect()->back();
        }

        // jika semua step selesai, update status surat + generate pdf cover
        if ($result['selesai']) {
            $surat->update(['status' => 'approved_owner']);

            // notify owner
            $this->notifService->send(
                $surat->user_id,
                'Surat Disetujui Penuh',
                "Surat dengan nomor {$surat->nomor_surat} telah disetujui sepenuhnya.",
                route('surat.show', $surat->id)
            );

            // generate pdf cover approval / stamp
            try {
                $documentType = 'surat_' . $surat->jenis_surat;
                $step = \App\Models\ApprovalStep::where('document_type', $documentType)->first();
                $ttdMode = $step?->ttd_mode ?? 'append';

                if ($ttdMode === 'stamp') {
                    $finalPath = $this->stampService->stamp($surat);
                    $surat->update(['final_pdf_path' => $finalPath]);
                    \Log::info('PDF Stamped: ' . $finalPath);
                } else {
                    $coverPath = $this->coverService->generateCover($surat);
                    $surat->update(['cover_pdf_path' => $coverPath]);
                    \Log::info('Cover generated: ' . $coverPath);
                    
                    $finalPath = $this->coverService->processMerge($surat);
                    if ($finalPath) {
                        $surat->update(['final_pdf_path' => $finalPath]);
                        \Log::info('Final PDF merged: ' . $finalPath);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gagal generate cover/stamp/merge PDF: ' . $e->getMessage());
            }
        } else {
            // notify next approver
            $nextStep = $surat->waitingStep();
            if ($nextStep) {
                $this->notifService->sendToJabatan(
                    $nextStep->jabatan,
                    'Menunggu Approval Surat',
                    "Ada surat baru ({$surat->nomor_surat}) yang menunggu approval Anda.",
                    route('surat.show', $surat->id)
                );
            }
        }

        flash()->success($result['message']);
        return redirect()->route('surat.show', $surat->id);
    }

    // ── reject ──────────────────────────────────────────────
    /** tolak surat */
    public function reject(Request $request, Surat $surat)
    {
        $request->validate([
            'catatan_revisi' => 'required|string|min:5|max:500',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi saat menolak.',
            'catatan_revisi.min'      => 'Catatan revisi minimal 5 karakter.',
        ]);

        // server-side guard: cek apakah giliran jabatan user ini
        $user = Auth::user()->load('profile');
        $documentType = 'surat_' . $surat->jenis_surat;
        if (!$this->approval->canApprove($documentType, $surat->id, $user)) {
            flash()->error('Bukan giliran Anda untuk menolak surat ini.');
            return redirect()->back();
        }

        $result = $this->approval->reject(
            $documentType,
            $surat->id,
            $user,
            $request->catatan_revisi
        );

        if (!$result['success']) {
            flash()->error($result['message']);
            return redirect()->back();
        }

        // notify owner about rejection
        $this->notifService->send(
            $surat->user_id,
            'Surat Ditolak / Perlu Revisi',
            "Surat dengan nomor {$surat->nomor_surat} ditolak oleh " . Auth::user()->name . ". Silakan cek catatan revisi.",
            route('surat.show', $surat->id)
        );

        // update status surat kembali ke 'revised'
        $surat->update([
            'status'         => 'revised',
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        flash()->success($result['message']);
        return redirect()->route('surat.show', $surat->id);
    }

    // ── download ────────────────────────────────────────────
    /** download surat */
    public function download(Surat $surat)
    {
        $this->authorize('download', $surat);

        if (!$surat->file_pdf) {
            flash()->error('File PDF tidak tersedia.');
            return redirect()->route('surat.show', $surat->id);
        }

        $filePath = storage_path('app/public/' . $surat->file_pdf);

        if (!file_exists($filePath)) {
            flash()->error('File PDF tidak ditemukan di server.');
            return redirect()->route('surat.show', $surat->id);
        }

        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $surat->nomor_surat) . '.pdf';

        return response()->download($filePath, $filename);
    }

    // ── delete ──────────────────────────────────────────────
    /** hapus surat */
    public function destroy(Surat $surat)
    {
        // manual check for now as we might not have a policy method yet
        if (Auth::id() !== $surat->user_id) {
            abort(403);
        }

        if (!$surat->canBeDeleted()) {
            flash()->error('Surat sudah dalam proses approval dan tidak dapat dihapus.');
            return redirect()->route('surat.show', $surat->id);
        }

        // hapus file
        if ($surat->file_pdf && file_exists(storage_path('app/public/' . $surat->file_pdf))) {
            unlink(storage_path('app/public/' . $surat->file_pdf));
        }

        $surat->delete();

        flash()->success('Surat berhasil dihapus.');
        return redirect()->route('surat.index');
    }

    // ── get ttd mode ───────────────────────────────────────
    public function getTtdMode(Request $request)
    {
        $jenisSurat = $request->jenis_surat;
        $step = \App\Models\ApprovalStep::where('document_type', 'surat_' . $jenisSurat)->first();
        $approvers = \App\Models\ApprovalStep::where('document_type', 'surat_' . $jenisSurat)
            ->orderBy('step_order')
            ->get()
            ->map(function($s) {
                return [
                    'jabatan' => $s->jabatan,
                    'label' => $s->label
                ];
            })
            ->toArray();

        return response()->json([
            'mode' => $step?->ttd_mode ?? 'append',
            'approvers' => $approvers
        ]);
    }

    // ── generate nomor surat ────────────────────────────────
    private function generateNomorSurat(): string
    {
        $count = Surat::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('SURAT/%d/%s/%03d', now()->year, now()->format('m'), $count);
    }
}