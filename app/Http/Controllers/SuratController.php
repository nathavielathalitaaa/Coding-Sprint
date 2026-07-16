<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Komisi;
use App\Models\Organisasi;
use App\Models\OrganisasiMember;
use App\Models\SuratKegiatanDetail;
use App\Services\ApprovalService;
use App\Services\PinVerificationService;
use App\Services\ApprovalCoverService;
use App\Services\PdfStampService;
use App\Services\NotificationService;
use App\Services\SuratNumberService;
use App\Services\DokumenValidationService;
use App\Models\SuratType;
use App\Models\ApprovalStep;
use App\Http\Requests\Surat\StoreSuratRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratController extends Controller
{
    public function __construct(
        private ApprovalService $approval,
        private PinVerificationService $pinService,
        private ApprovalCoverService $coverService,
        private PdfStampService $stampService,
        private NotificationService $notifService,
        private SuratNumberService $numberService,
        private DokumenValidationService $validationService,
    ) {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the documents.
     * Filters visibility based on user roles and positions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Surat::with(['user', 'approvals', 'suratType', 'organisasi', 'proposalFormatCheck']);

        // ── Filter visibilitas berdasarkan role ─────────────────────────
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            // Admin lihat semua surat
        } elseif ($user->hasAnyRole(['pengawas_pusat', 'kepala_sekolah'])) {
            // Role global: lihat surat yang memiliki step global waiting untuk mereka
            $roleName = $user->hasRole('pengawas_pusat') ? 'pengawas_pusat' : 'kepala_sekolah';
            $query->where(function($q) use ($user, $roleName) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('approvals', fn($sq) =>
                      $sq->where('target_mode', 'global')->where('jabatan', $roleName)
                  );
            });
        } else {
            // Guru / Anggota: lihat surat milik sendiri + surat dari organisasi yang diikuti
            $userOrganisasis = Organisasi::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();
            $organisasiIds = $userOrganisasis->pluck('id');
            $isMpk = $userOrganisasis->where('tipe', 'mpk')->isNotEmpty();
            $isOsis = $userOrganisasis->where('tipe', 'osis')->isNotEmpty();

            $query->where(function($q) use ($user, $organisasiIds, $isMpk, $isOsis) {
                $q->where('user_id', $user->id);
                
                if ($organisasiIds->isNotEmpty()) {
                    $q->orWhereIn('organisasi_id', $organisasiIds);
                }
                
                // assigned_user_id spesifik atau sudah pernah approve
                $q->orWhereHas('approvals', function($sq) use ($user, $isMpk, $isOsis) {
                    $sq->where('assigned_user_id', $user->id)
                       ->orWhere('approver_id', $user->id);
                       
                    if ($isMpk) {
                        $sq->orWhere('target_mode', 'fixed_mpk');
                    }
                    if ($isOsis) {
                        $sq->orWhere('target_mode', 'fixed_osis');
                    }
                });
            });
        }

        // ── Filter tambahan: Persetujuan Saya (waiting) ──────────────────
        $filter = $request->input('filter');
        if ($filter === 'waiting') {
            $query->where('status', 'submitted');
            $query->whereHas('approvals', function($q) use ($user) {
                $q->where('status', 'waiting')
                  ->where(function($sq) use ($user) {
                      $sq->where('assigned_user_id', $user->id);
                      
                      $userOrganisasis = Organisasi::whereHas('members', fn($mq) => $mq->where('user_id', $user->id))->get();
                      $organisasiIds = $userOrganisasis->pluck('id');
                      $isMpk = $userOrganisasis->where('tipe', 'mpk')->isNotEmpty();
                      $isOsis = $userOrganisasis->where('tipe', 'osis')->isNotEmpty();
                      
                      $sq->orWhere(function($ssq) use ($user, $organisasiIds, $isMpk, $isOsis) {
                          $ssq->whereNull('assigned_user_id');
                          
                          $jabatans = $user->organisasiMembers()->pluck('jabatan')->filter()->unique()->toArray();
                          if (in_array('bph', $jabatans)) {
                              $jabatans[] = 'bph_osis';
                              $jabatans[] = 'bph_mpk';
                          }
                          if (!empty($jabatans)) {
                              $ssq->whereIn('jabatan', $jabatans);
                          }
                          
                          $ssq->where(function($sssq) use ($user, $isMpk, $isOsis) {
                              $sssq->where('target_mode', 'submitter');
                              if ($isMpk) $sssq->orWhere('target_mode', 'fixed_mpk');
                              if ($isOsis) $sssq->orWhere('target_mode', 'fixed_osis');
                              if ($user->hasRole('pengawas_pusat')) $sssq->orWhere('target_mode', 'global')->where('jabatan', 'pengawas_pusat');
                              if ($user->hasRole('kepala_sekolah')) $sssq->orWhere('target_mode', 'global')->where('jabatan', 'kepala_sekolah');
                          });
                      });
                  });
            });
        }

        // ── Filter tambahan: Pencarian kata kunci ────────────────────────
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('perihal', 'like', '%' . $search . '%')
                  ->orWhere('nomor_surat', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $surats = $query->latest()->paginate(15)->withQueryString();

        return view('surat.index', compact('surats'));
    }


    /**
     * Show the form for creating a new document.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Surat::class);

        $suratTypes  = SuratType::where('is_active', true)->get();
        $organisasis = Organisasi::where('is_active', true)->orderBy('tipe')->get();
        $komisis     = Komisi::where('is_active', true)->with('organisasi')->get();

        return view('surat.create', compact('suratTypes', 'organisasis', 'komisis'));
    }


    /**
     * Store a newly created document in storage.
     *
     * @param StoreSuratRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSuratRequest $request)
    {
        // ── Otorisasi ───────────────────────────────────────────────────
        $this->authorize('store', Surat::class);

        $suratType = SuratType::findOrFail($request->surat_type_id);

        // ── Validasi Organisasi Tipe ────────────────────────────────────
        $organisasi = \App\Models\Organisasi::findOrFail($request->organisasi_id);
        if ($suratType->organisasi_id && $suratType->organisasi_id !== $organisasi->id) {
            return back()->withErrors(['surat_type_id' => 'Jenis surat ini tidak berlaku untuk organisasi yang dipilih.'])->withInput();
        }
        if ($suratType->organisasi_tipe && $suratType->organisasi_tipe !== $organisasi->tipe) {
            return back()->withErrors(['surat_type_id' => 'Jenis surat ini tidak berlaku untuk organisasi yang dipilih.'])->withInput();
        }

        // ── Validasi tambahan: komisi wajib untuk surat MPK ────────────
        if ($suratType->organisasi_tipe === 'mpk' && !$request->komisi_id) {
            return back()->withErrors(['komisi_id' => 'Komisi wajib dipilih untuk surat MPK.'])->withInput();
        }

        // ── Validasi Duplikat & Konflik Jadwal (hanya untuk surat dengan detail kegiatan) ──
        if ($suratType->requires_kegiatan_detail) {

            // Node 1: Deteksi Duplikat — kegiatan serupa dari organisasi yang sama dalam ±7 hari
            $duplikat = $this->validationService->cekDuplikat(
                $request->nama_kegiatan,
                $request->organisasi_id,
                $request->tanggal_mulai
            );

            if ($duplikat) {
                return back()->withErrors([
                    'nama_kegiatan' => "Terdeteksi kegiatan serupa (kemiripan {$duplikat['percent']}%): \"{$duplikat['nama_kegiatan']}\" pada {$duplikat['tanggal_mulai']} (No. Surat: {$duplikat['nomor_surat']}). Jika ini kegiatan yang berbeda, ubah nama kegiatan agar lebih spesifik, atau periksa kembali pengajuan sebelumnya.",
                ])->withInput();
            }

            // Node 2: Deteksi Konflik Jadwal — lokasi yang sama di rentang tanggal yang overlap (lintas organisasi)
            $konflik = $this->validationService->cekKonflikJadwal(
                $request->lokasi,
                $request->tanggal_mulai,
                $request->tanggal_selesai
            );

            if ($konflik) {
                $tglSelesaiLabel = $konflik['tanggal_selesai'] ?? $konflik['tanggal_mulai'];
                return back()->withErrors([
                    'lokasi' => "Lokasi \"{$request->lokasi}\" sudah dipakai oleh kegiatan \"{$konflik['nama_kegiatan']}\" ({$konflik['organisasi_nama']}) pada tanggal yang sama/berdekatan ({$konflik['tanggal_mulai']} - {$tglSelesaiLabel}). Silakan pilih lokasi atau tanggal lain.",
                ])->withInput();
            }
        }

        // ── Proses Upload File ──────────────────────────────────────────
        $fileName = null;
        if ($request->hasFile('file_pdf')) {
            $fileName = $request->file('file_pdf')->store('surat', 'public');
        }

        // ── Penyimpanan Data ────────────────────────────────────────────
        $surat = Surat::create([
            'user_id'         => Auth::id(),
            'surat_type_id'   => $suratType->id,
            'organisasi_id'   => $request->organisasi_id,
            'komisi_id'       => $suratType->organisasi_tipe === 'mpk' ? $request->komisi_id : null,
            'nomor_surat'     => null, // Diisi nanti oleh admin
            'jenis_surat'     => $suratType->kode,
            'perihal'         => $request->perihal,
            'file_pdf'        => $fileName,
            'ttd_coordinates' => $request->ttd_coordinates ? json_decode($request->ttd_coordinates, true) : null,
            'status'          => 'pending_admin',
        ]);

        // JANGAN panggil initFromSuratType di sini. Biarkan admin yang melakukan Disposisi Awal
        // $this->approval->initFromSuratType($surat);

        // ── Simpan Detail Kegiatan (jika surat type memerlukannya) ──────
        if ($suratType->requires_kegiatan_detail) {
            SuratKegiatanDetail::create([
                'surat_id'          => $surat->id,
                'nama_kegiatan'     => $request->nama_kegiatan,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai ?: null,
                'lokasi'            => $request->lokasi,
                'deskripsi_singkat' => $request->deskripsi_singkat ?: null,
            ]);

            // Run proposal format check synchronously
            try {
                $checker = app(\App\Services\ProposalFormatChecker::class);
                $checker->analyze($surat);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Proposal format check failed: ' . $e->getMessage());
            }
        }

        flash()->success('Surat berhasil diajukan dan sedang menunggu verifikasi Admin Sekretariat.');
        return redirect()->route('surat.show', $surat->id);
    }


    /**
     * Display the specified document.
     *
     * @param Surat $surat
     * @return \Illuminate\View\View
     */
    public function show(Surat $surat)
    {
        // ── Otorisasi ───────────────────────────────────────────────────
        $this->authorize('view', $surat);

        // ── Pengambilan Data Approval ───────────────────────────────────
        $documentType = 'surat_' . $surat->jenis_surat;
        $steps = $this->approval->getStatus($documentType, $surat->id);
        $authUser = Auth::user()->load(['organisasiMembers', 'komisiMembers']);
        $canApprove = $this->approval->canApprove($documentType, $surat->id, $authUser);
        $waitingStep = $this->approval->getWaitingStep($documentType, $surat->id);
        
        $this->approval->markAsRead($documentType, $surat->id, $authUser);

        return view('surat.show', compact('surat', 'steps', 'canApprove', 'waitingStep'));
    }


    /**
     * Show the form for editing the specified document.
     *
     * @param Surat $surat
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Surat $surat)
    {
        // ── Otorisasi & Validasi Status ─────────────────────────────────
        $this->authorize('edit', $surat);

        if (!$surat->canBeEdited()) {
            flash()->error('Surat sudah dalam proses approval dan tidak dapat diubah.');
            return redirect()->route('surat.show', $surat->id);
        }

        return view('surat.edit', compact('surat'));
    }


    /**
     * Update the specified document in storage and resubmit for approval.
     *
     * @param StoreSuratRequest $request
     * @param Surat $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreSuratRequest $request, Surat $surat)
    {
        // ── Otorisasi & Validasi Status ─────────────────────────────────
        $this->authorize('update', $surat);

        if (!$surat->canBeEdited()) {
            flash()->error('Surat sudah dalam proses approval dan tidak dapat diubah.');
            return redirect()->route('surat.show', $surat->id);
        }

        // ── Penggantian File PDF ────────────────────────────────────────
        if ($request->hasFile('file_pdf')) {
            if ($surat->file_pdf && file_exists(storage_path('app/public/' . $surat->file_pdf))) {
                unlink(storage_path('app/public/' . $surat->file_pdf));
            }
            
            $surat->update(['file_pdf' => $request->file('file_pdf')->store('surat', 'public')]);
        }

        // ── Reset Status & Approval ─────────────────────────────────────
        $surat->update([
            'status'         => 'submitted',
            'catatan_revisi' => null,
        ]);

        if ($surat->suratType && $surat->suratType->requires_kegiatan_detail) {
            try {
                $checker = app(\App\Services\ProposalFormatChecker::class);
                $checker->analyze($surat);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Proposal format check failed during update: ' . $e->getMessage());
            }
        }

        $this->approval->resubmit('surat_' . $surat->jenis_surat, $surat->id);

        flash()->success('Surat berhasil direvisi dan dikirim ulang untuk approval.');
        return redirect()->route('surat.show', $surat->id);
    }


    /**
     * Approve the document at the current step.
     *
     * @param Request $request
     * @param Surat $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Surat $surat)
    {
        $user = Auth::user()->load(['organisasiMembers', 'komisiMembers']);
        $documentType = 'surat_' . $surat->jenis_surat;

        // ── Cek apakah step saat ini memerlukan tanda tangan ────────────
        $requiresSignature = $this->approval->currentStepRequiresSignature($documentType, $surat->id);

        // ── Validasi Request (PIN hanya wajib jika is_signer = true) ────
        $rules = [
            'catatan' => 'nullable|string|max:500',
        ];

        if ($requiresSignature) {
            $rules['pin'] = 'required|string';
        }

        $request->validate($rules, [
            'pin.required' => 'PIN wajib diisi untuk menyetujui surat.',
        ]);

        // ── Verifikasi PIN (hanya jika is_signer = true) ────────────────
        if ($requiresSignature) {
            if (!$this->pinService->verify($user, $request->pin)) {
                flash()->error('PIN salah. Silakan coba lagi.');
                return redirect()->back();
            }
        }

        // ── Pengecekan Otorisasi Approval ───────────────────────────────
        if (!$this->approval->canApprove($documentType, $surat->id, $user)) {
            flash()->error('Bukan giliran Anda untuk approve surat ini.');
            return redirect()->back();
        }

        // ── Proses Approval ─────────────────────────────────────────────
        $ttdSnapshot = $requiresSignature ? $this->pinService->getTtdPath($user) : null;

        $approvalResult = $this->approval->approve(
            $documentType,
            $surat->id,
            $user,
            $request->catatan ?? '',
            $ttdSnapshot
        );

        if (!$approvalResult['success']) {
            flash()->error($approvalResult['message']);
            return redirect()->back();
        }

        // ── Penanganan Penyelesaian Approval ────────────────────────────
        if ($approvalResult['selesai']) {
            $surat->update(['status' => 'approved_owner']);

            if ($surat->suratType && $surat->suratType->requires_kegiatan_detail) {
                $surat->update([
                    'pic_user_id' => $surat->user_id,
                    'status_pelaksanaan' => 'belum_mulai',
                ]);
            }

            $this->notifService->send(
                $surat->user_id,
                'Surat Disetujui Penuh',
                "Surat dengan nomor {$surat->nomor_surat} telah disetujui sepenuhnya.",
                route('surat.show', $surat->id)
            );

            // Broadcast ke semua approver lain
            $approverIds = \App\Models\DocumentApproval::where('document_type', $documentType)
                ->where('document_id', $surat->id)
                ->whereNotNull('approver_id')
                ->pluck('approver_id')
                ->unique()
                ->reject(fn($id) => $id == $surat->user_id);

            foreach ($approverIds as $apprId) {
                $this->notifService->send(
                    $apprId,
                    'Surat Disetujui Penuh',
                    "Surat dengan nomor {$surat->nomor_surat} yang Anda setujui telah disetujui sepenuhnya oleh seluruh rantai approval.",
                    route('surat.show', $surat->id)
                );
            }

            try {
                $documentType = 'surat_' . $surat->jenis_surat;
                
                if ($surat->suratType) {
                    $ttdMode = 'stamp';
                } else {
                    $step = ApprovalStep::where('document_type', $documentType)->first();
                    $ttdMode = $step?->ttd_mode ?? 'append';
                }

                if ($ttdMode === 'stamp') {
                    $finalPath = $this->stampService->stamp($surat);
                    $surat->update(['final_pdf_path' => $finalPath]);
                } else {
                    $coverPath = $this->coverService->generateCover($surat);
                    $surat->update(['cover_pdf_path' => $coverPath]);
                    $surat->refresh();
                    
                    $finalPath = $this->coverService->processMerge($surat);
                    
                    if ($finalPath) {
                        $surat->update(['final_pdf_path' => $finalPath]);
                        $surat->refresh();
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Gagal generate cover/stamp/merge PDF: ' . $e->getMessage());
                flash()->warning('Surat disetujui, namun PDF dengan tanda tangan gagal digabungkan: ' . $e->getMessage());
            }
        } else {
            // ── Notifikasi Approver Berikutnya ──────────────────────────
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

        flash()->success($approvalResult['message']);
        return redirect()->route('surat.show', $surat->id);
    }


    /**
     * Reject the document and return it to the author for revision.
     *
     * @param Request $request
     * @param Surat $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Surat $surat)
    {
        // ── Validasi Request ────────────────────────────────────────────
        $request->validate([
            'catatan_revisi' => 'required|string|min:1|max:500',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi saat menolak.',
            'catatan_revisi.min'      => 'Catatan revisi minimal 1 karakter.',
        ]);

        // ── Pengecekan Otorisasi Rejection ──────────────────────────────
        $user = Auth::user()->load(['organisasiMembers', 'komisiMembers']);
        $documentType = 'surat_' . $surat->jenis_surat;
        
        if (!$this->approval->canApprove($documentType, $surat->id, $user)) {
            flash()->error('Bukan giliran Anda untuk menolak surat ini.');
            return redirect()->back();
        }

        // ── Proses Rejection ────────────────────────────────────────────
        $approvalResult = $this->approval->reject(
            $documentType,
            $surat->id,
            $user,
            $request->catatan_revisi
        );

        if (!$approvalResult['success']) {
            flash()->error($approvalResult['message']);
            return redirect()->back();
        }

        $this->notifService->send(
            $surat->user_id,
            'Surat Ditolak / Perlu Revisi',
            "Surat dengan nomor {$surat->nomor_surat} ditolak oleh " . Auth::user()->name . ". Silakan cek catatan revisi.",
            route('surat.show', $surat->id)
        );

        $surat->update([
            'status'         => 'revised',
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        flash()->success($approvalResult['message']);
        return redirect()->route('surat.show', $surat->id);
    }


    /**
     * Download the specified document file.
     *
     * @param Surat $surat
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Surat $surat, Request $request)
    {
        // ── Otorisasi ───────────────────────────────────────────────────
        $this->authorize('download', $surat);

        $type = $request->query('type', 'auto');

        // ── Penentuan Path File ─────────────────────────────────────────
        $relativePath = match($type) {
            'original' => $surat->file_pdf,
            'cover'    => $surat->cover_pdf_path,
            'final'    => $surat->final_pdf_path,
            default    => $surat->final_pdf_path ?? $surat->cover_pdf_path ?? $surat->file_pdf
        };

        if (!$relativePath) {
            flash()->error('File PDF tidak tersedia.');
            return redirect()->route('surat.show', $surat->id);
        }

        $filePath = storage_path('app/public/' . $relativePath);

        if (!file_exists($filePath)) {
            flash()->error('File PDF tidak ditemukan di server.');
            return redirect()->route('surat.show', $surat->id);
        }

        // ── Formatting Nama File ────────────────────────────────────────
        $baseName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $surat->nomor_surat);
        
        $suffix = '';
        if ($type === 'original') {
            $suffix = '_original';
        } elseif ($type === 'cover') {
            $suffix = '_approval_sheet';
        } elseif (($surat->hasFinalPdf() && ($type === 'auto' || $type === 'final')) || ($surat->cover_pdf_path && $type === 'cover')) {
            $suffix = '_signed';
        }

        $filename = $baseName . $suffix . '.pdf';

        return response()->download($filePath, $filename);
    }


    /**
     * Remove the specified document from storage.
     *
     * @param Surat $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Surat $surat)
    {
        // ── Otorisasi & Validasi ────────────────────────────────────────
        if (Auth::id() !== $surat->user_id) {
            abort(403);
        }

        if (!$surat->canBeDeleted()) {
            flash()->error('Surat sudah dalam proses approval dan tidak dapat dihapus.');
            return redirect()->route('surat.show', $surat->id);
        }

        // ── Proses Penghapusan File & Data ──────────────────────────────
        $filesToDelete = [
            $surat->file_pdf,
            $surat->cover_pdf_path,
            $surat->final_pdf_path
        ];

        foreach ($filesToDelete as $file) {
            if ($file && file_exists(storage_path('app/public/' . $file))) {
                unlink(storage_path('app/public/' . $file));
            }
        }

        $surat->delete();

        flash()->success('Surat berhasil dihapus.');
        return redirect()->route('surat.index');
    }


    /**
     * Get the signature mode and approvers for a document type.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTtdMode(Request $request)
    {
        $jenis = $request->jenis_surat;
        
        $suratType = SuratType::where('kode', $jenis)->first();
        
        if ($suratType) {
            $approvers = $suratType->approvers()
                ->where('is_signer', true)
                ->get()
                ->map(function($a) {
                    return [
                        'jabatan' => $a->jabatan_label,
                        'label'   => $a->label,
                    ];
                })->toArray();

            return response()->json([
                'mode' => 'stamp',
                'approvers' => $approvers
            ]);
        }

        $step = ApprovalStep::where('document_type', 'surat_' . $jenis)
            ->orWhere('document_type', $jenis)
            ->first();

        $approvers = ApprovalStep::where(function($q) use ($jenis) {
                $q->where('document_type', 'surat_' . $jenis)
                  ->orWhere('document_type', $jenis);
            })
            ->where('is_signer', true)
            ->orderBy('step_order')
            ->get()
            ->map(function($s) {
                return [
                    'jabatan' => $s->jabatan,
                    'label'   => $s->label,
                ];
            })
            ->toArray();

        return response()->json([
            'mode' => $step?->ttd_mode ?? 'append',
            'approvers' => $approvers
        ]);
    }


    /**
     * Provide a preview of the signature image for a specific role.
     *
     * @param string $jabatan
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getTtdPreview(string $jabatan)
    {
        $profile = EmployeeProfile::where('jabatan', $jabatan)
            ->whereNotNull('ttd_path')
            ->first();

        if (!$profile) {
            return response('', 204);
        }

        $paths = [
            storage_path('app/private/private/' . $profile->ttd_path),
            storage_path('app/private/' . $profile->ttd_path),
        ];

        $path = null;
        
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }

        if (!$path) {
            return response('', 204);
        }

        return response()->file($path, [
            'Content-Type'  => mime_content_type($path),
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    /**
     * Tampilkan daftar surat yang berstatus pending_admin (Fase Review Admin).
     */
    public function inboxAdmin()
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Hanya Admin yang dapat mengakses Inbox Admin.');
        }

        $surats = Surat::where('status', 'pending_admin')->latest()->paginate(15);
        return view('surat.inbox-admin', compact('surats'));
    }

    /**
     * Proses verifikasi Admin: Tolak (reject) atau Setujui (teruskan / Disposisi Awal).
     */
    public function verifikasiAdmin(Request $request, Surat $surat)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Hanya Admin yang dapat memverifikasi surat.');
        }

        if ($surat->status !== 'pending_admin') {
            flash()->error('Surat ini tidak sedang menunggu verifikasi admin.');
            return back();
        }

        if ($request->action === 'reject') {
            $request->validate(['catatan_revisi' => 'required|string|max:500']);
            $surat->update([
                'status' => 'rejected_admin',
                'catatan_revisi' => $request->catatan_revisi
            ]);
            flash()->warning('Surat ditolak dan dikembalikan ke pembuat.');
        } else {
            // ── Disposisi Awal ──────────────────────────────────────
            // Nomor surat bisa diisi manual oleh admin, atau
            // digenerate otomatis oleh SuratNumberService jika kosong.

            if (!$surat->suratType) {
                flash()->error('Jenis surat tidak ditemukan. Tidak dapat memproses disposisi.');
                return back();
            }

            $nomorManual = trim($request->input('nomor_surat', ''));
            if ($nomorManual) {
                $request->validate([
                    'nomor_surat' => 'required|string|max:100',
                ]);
            }

            DB::transaction(function () use ($surat, $nomorManual) {
                // Gunakan nomor manual jika diisi, jika tidak generate otomatis
                $nomorSurat = $nomorManual ?: $this->numberService->generate($surat->suratType);

                $surat->update([
                    'nomor_surat'    => $nomorSurat,
                    'status'         => 'submitted',
                    'catatan_revisi' => null,
                ]);

                // Inisialisasi Alur Approval
                $this->approval->initFromSuratType($surat);

                // Kirim Notifikasi ke Approver Pertama
                $firstStep = $surat->approvals()->where('status', 'waiting')->first();
                if ($firstStep) {
                    $this->notifService->sendToJabatan(
                        $firstStep->jabatan,
                        'Permintaan Approval Surat Baru',
                        "Surat ({$surat->nomor_surat}) telah diverifikasi admin dan menunggu persetujuan Anda.",
                        route('surat.show', $surat->id)
                    );
                }
            });

            $pesanNomor = $nomorManual ? "Nomor surat ditetapkan: {$nomorManual}." : 'Nomor surat digenerate otomatis.';
            flash()->success("Surat diverifikasi. {$pesanNomor} Diteruskan ke alur persetujuan.");
        }

        return redirect()->route('surat.inbox_admin');
    }


    // ══════════════════════════════════════════════════════════════════
    // ENDPOINT REAL-TIME VALIDATION (AJAX)
    // Dipanggil dari create.blade.php via fetch() — hanya untuk UX preview.
    // Validasi final (blocking) tetap di store() via DokumenValidationService.
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET /surat/cek-duplikat
     * Params: nama_kegiatan, tanggal_mulai, organisasi_id
     * Response: { duplikat: null | { surat_id, nomor_surat, nama_kegiatan, tanggal_mulai, percent } }
     */
    public function cekDuplikat(Request $request)
    {
        // Validasi minimal agar tidak error saat field belum terisi
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'organisasi_id' => 'required|integer',
        ]);

        $duplikat = $this->validationService->cekDuplikat(
            $request->nama_kegiatan,
            (int) $request->organisasi_id,
            $request->tanggal_mulai
        );

        return response()->json([
            'duplikat' => $duplikat,
            'rekomendasi' => $duplikat
                ? "Kegiatan \"{$duplikat['nama_kegiatan']}\" ({$duplikat['percent']}% mirip) sudah diajukan dengan nomor {$duplikat['nomor_surat']} pada {$duplikat['tanggal_mulai']}. Pertimbangkan untuk mengganti nama kegiatan agar lebih spesifik, atau pastikan ini bukan pengajuan ganda."
                : null,
        ]);
    }

    /**
     * GET /surat/cek-konflik
     * Params: lokasi, tanggal_mulai, tanggal_selesai (opsional)
     * Response: { konflik: null | { surat_id, organisasi_nama, nomor_surat, nama_kegiatan, tanggal_mulai, tanggal_selesai } }
     */
    public function cekKonflik(Request $request)
    {
        $request->validate([
            'lokasi'         => 'required|string|max:255',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'nullable|date',
        ]);

        $konflik = $this->validationService->cekKonflikJadwal(
            $request->lokasi,
            $request->tanggal_mulai,
            $request->tanggal_selesai ?: null
        );

        return response()->json([
            'konflik' => $konflik,
            'rekomendasi' => $konflik
                ? "Lokasi \"{$request->lokasi}\" sudah dipakai oleh {$konflik['organisasi_nama']} untuk kegiatan \"{$konflik['nama_kegiatan']}\" pada {$konflik['tanggal_mulai']}" . ($konflik['tanggal_selesai'] ? " s/d {$konflik['tanggal_selesai']}" : "") . ". Pilih lokasi lain atau ubah jadwal kegiatan Anda."
                : null,
        ]);
    }
}
