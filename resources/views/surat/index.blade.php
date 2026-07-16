@extends('layouts.master')

<style>
  /* ── Responsive: Mobile ── */
  @media (max-width: 639px) {
    .surat-actions {
      width: 100%;
    }
    .surat-actions button, .surat-actions a {
      width: 100%;
      min-height: 44px;
      justify-content: center;
    }
  }

  /* ── Header Layout Fix ── */
  .custom-header-fix {
    display: flex !important;
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 16px !important;
  }
  .custom-header-title {
    flex: 1 !important;
    min-width: 0 !important;
  }
  .custom-header-btn {
    flex-shrink: 0 !important;
    width: auto !important;
  }
  .custom-header-btn a {
    white-space: nowrap !important;
    width: auto !important;
    color: #ffffff !important;
    background-color: var(--color-primary) !important;
    border-radius: 16px !important;
  }
  .custom-header-btn a:hover {
    background-color: var(--color-primary-dark) !important;
  }
</style>

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4 custom-header-fix">
      <div class="mb-8">
        @if(request('filter') === 'waiting')
          <h1 class="text-3xl font-sans font-bold text-[#111111]">Persetujuan Saya</h1>
          <p class="text-[13px] font-light text-[#6B7280] mt-1">Surat yang menunggu tindakan persetujuan dari Anda</p>
        @else
          <h1 class="text-3xl font-sans font-bold text-[#111111]">Daftar Surat</h1>
          <p class="text-[13px] font-light text-[#6B7280] mt-1">Semua surat yang dapat Anda akses</p>
        @endif
      </div>
      @can('create', App\Models\Surat::class)
      <div class="w-full sm:w-auto shrink-0 custom-header-btn">
          <a href="{{ route('surat.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition shadow-sm">
              <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Surat Baru
          </a>
      </div>
      @endcan
    </div>

    {{-- Tab filter --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('surat.index') }}"
           class="px-4 py-2 rounded-2xl text-sm font-semibold transition
                  {{ request('filter') !== 'waiting' ? 'bg-[#111111] text-white' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">
            Semua Surat
        </a>
        <a href="{{ route('surat.index', ['filter' => 'waiting']) }}"
           class="px-4 py-2 rounded-2xl text-sm font-semibold transition flex items-center gap-2
                  {{ request('filter') === 'waiting' ? 'bg-[var(--color-primary)] text-white' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">
            Menunggu Saya
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 px-4 py-3 relative text-base text-green-800 bg-green-50 rounded-lg" role="alert">
            {{ $message }}
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="mb-4 px-4 py-3 relative text-base text-red-800 bg-red-50 rounded-lg" role="alert">
            {{ $message }}
        </div>
    @endif


    <div class="w-full">
        <div class="skeleton-wrapper w-full">
            <div class="space-y-4">
                @for($i=0; $i<4; $i++)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white rounded-[28px] shadow-sm border border-gray-100 px-6 py-5 gap-4">
                    <div class="flex items-center gap-4 w-full">
                        <div class="skeleton w-12 h-12 rounded-full flex-shrink-0"></div>
                        <div class="flex-grow space-y-2">
                            <div class="skeleton h-4 w-1/3"></div>
                            <div class="skeleton h-3 w-1/4"></div>
                            <div class="skeleton h-3 w-1/5"></div>
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <div class="skeleton h-8 w-20 rounded-2xl"></div>
                        <div class="skeleton h-8 w-20 rounded-2xl"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
        <div class="real-content hidden w-full">
          <div class="space-y-4">

    @inject('approvalService', 'App\Services\ApprovalService')

    @forelse($surats as $key => $surat)
        @php
            $user = auth()->user();
            $isMyTurn = false;
            $isSigner = true;
            
            if ($surat->status === 'submitted') {
                $isMyTurn = $approvalService->canApprove('surat_' . $surat->jenis_surat, $surat->id, $user);
                if ($isMyTurn) {
                    $waitingStep = $approvalService->getWaitingStep('surat_' . $surat->jenis_surat, $surat->id);
                    if ($waitingStep) {
                        $isSigner = (bool)$waitingStep->is_signer;
                    }
                }
            }
            $bisaApprove = $isMyTurn;
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white rounded-[28px] shadow-sm border border-gray-100 hover:shadow-md transition px-6 py-5 gap-4 {{ $isMyTurn ? 'border-l-4 border-l-red-400' : '' }}">
            
            <!-- left content (keep data same) -->
            <div class="flex items-center gap-4">
                <!-- avatar -->
                <div class="w-12 h-12 rounded-full bg-[var(--color-primary)]/30 flex items-center justify-center font-semibold text-[var(--color-text)] text-lg flex-shrink-0">
                    {{ strtoupper(substr($surat->user->name ?? 'U',0,1)) }}
                </div>

                <div>
                    <p class="font-semibold text-gray-800 text-base">
                        {{ $surat->user->name ?? 'Unknown' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $surat->suratType ? $surat->suratType->nama : ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }} 
                        @if($surat->organisasi)
                            &bull; <span class="text-[var(--color-text)] font-medium">{{ $surat->organisasi->nama }}</span>
                        @endif
                        <br>
                        <span class="text-xs">{{ $surat->nomor_surat }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $surat->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- right actions -->
            <div class="flex flex-col sm:items-end gap-3 sm:gap-2 border-t sm:border-0 pt-3 sm:pt-0 border-gray-100">
                <div class="flex items-center gap-2">
                    @if($surat->status === 'submitted')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Diajukan</span>
                    @elseif($surat->status === 'approved_owner')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Disetujui Sepenuhnya</span>
                    @elseif($surat->status === 'rejected')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Ditolak</span>
                    @elseif($surat->status === 'revised')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Butuh Revisi</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($surat->status) }}</span>
                    @endif

                    @if($surat->suratType && $surat->suratType->requires_kegiatan_detail && $surat->proposalFormatCheck)
                        @php
                            $pScore = $surat->proposalFormatCheck->skor_akhir;
                            $pColor = $pScore >= 70 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pColor }}">
                            Proposal: {{ $pScore }}
                        </span>
                    @endif
                </div>

                <div class="surat-actions flex flex-col sm:flex-row gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                    @can('view', $surat)
                    <a href="{{ route('surat.show', $surat->id) }}"
                       class="px-4 py-2 rounded-2xl border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition flex items-center justify-center">
                        Lihat
                    </a>
                    @endcan
 
                    @if($bisaApprove)
                    <button type="button"
                        onclick="quickApprove('{{ route('surat.approve', $surat->id) }}', {{ $isSigner ? 'true' : 'false' }}, '{{ route('surat.show', $surat->id) }}')"
                        class="px-4 py-2 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-medium hover:bg-[var(--color-primary-dark)] transition shadow-sm flex items-center justify-center">
                        <i data-lucide="check" class="w-4 h-4 mr-1"></i> Setujui
                    </button>
                    <button type="button"
                        onclick="quickReject('{{ route('surat.reject', $surat->id) }}')"
                        class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-2xl text-sm font-medium hover:bg-red-100 transition flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4 mr-1"></i> Tolak
                    </button>
                    @endif
                </div>
            </div>
        </div>

    @empty
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-[var(--color-primary)]/10 rounded-[28px] flex items-center justify-center mx-auto mb-4">
                <i data-lucide="file-text" class="w-8 h-8 text-[var(--color-primary)]"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Surat</h3>
            <p class="text-sm text-gray-500 mb-6">Ajukan surat baru untuk memulai alur persetujuan.</p>
            @can('create', App\Models\Surat::class)
            <a href="{{ route('surat.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i> Ajukan Surat Pertama
            </a>
            @endcan
        </div>
    @endforelse

    </div>

    <div class="mt-6">
        {{ $surats->links() }}
    </div>

          </div>{{-- /space-y-4 real-content --}}
        </div>{{-- /real-content --}}
    </div>{{-- /w-full --}}


@push('modals')
{{-- modal approve --}}
<div id="modalApprove" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-[28px] shadow-2xl w-full max-w-[400px] mx-4 overflow-hidden">
        <div class="px-7 pt-7 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:#FFF1F2;">
                    <i data-lucide="shield-check" class="w-5 h-5" style="color:#E62129;"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-[#111111] leading-tight" id="approveModalTitle">Setujui Surat</h3>
                    <p class="text-[11px] text-gray-400" id="approveModalSub">Konfirmasi persetujuan dokumen</p>
                </div>
                <button type="button" onclick="closeModals()" class="ml-auto text-gray-300 hover:text-gray-500 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="h-px bg-gray-100 mx-7"></div>
        
        {{-- Form untuk Approver Biasa (Tanpa PIN) --}}
        <form id="formApprove" method="POST" class="px-7 pb-7 pt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Catatan <span class="font-normal normal-case">(opsional)</span></label>
                <textarea name="catatan" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 text-[#111111] rounded-2xl text-sm outline-none focus:ring-2 resize-none placeholder-gray-300" placeholder="Tambahkan catatan jika ada..."></textarea>
            </div>
            
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModals()" class="flex-1 py-3 border border-gray-200 text-gray-500 rounded-2xl text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="flex-1 py-2.5 px-5 text-white rounded-2xl text-sm font-bold transition flex items-center justify-center gap-2" style="background:#E62129;">
                    <i data-lucide="check" class="w-4 h-4 flex-shrink-0"></i>
                    <span>Setujui</span>
                </button>
            </div>
        </form>

        {{-- Peringatan untuk Signer TTD --}}
        <div id="signerWarning" class="px-7 pb-7 pt-5 space-y-4 hidden">
            <p class="text-sm text-gray-600 leading-relaxed">
                Dokumen ini memerlukan <strong>Tanda Tangan Digital</strong> Anda. Proses pembubuhan tanda tangan harus dilakukan melalui halaman rincian dokumen.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeModals()" class="flex-1 py-3 border border-gray-200 text-gray-500 rounded-2xl text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
                <a id="btnGoToDetail" href="#" class="flex-1 py-3 text-white rounded-2xl text-sm font-bold transition flex items-center justify-center gap-2" style="background:#E62129;">
                    <i data-lucide="external-link" class="w-4 h-4 flex-shrink-0"></i>
                    Buka Detail
                </a>
            </div>
        </div>
    </div>
</div>

{{-- modal reject --}}
<div id="modalReject" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-[28px] shadow-2xl w-full max-w-[400px] mx-4 overflow-hidden">
        <div class="px-7 pt-7 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-[#111111]">Tolak Surat</h3>
                    <p class="text-[11px] text-gray-400">Berikan alasan penolakan</p>
                </div>
                <button type="button" onclick="closeModals()" class="ml-auto text-gray-300 hover:text-gray-500 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div class="h-px bg-gray-100 mx-7"></div>
        <form id="formReject" method="POST" class="px-7 pb-7 pt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Alasan Penolakan <span style="color:#E62129;">*</span></label>
                <textarea name="catatan_revisi" rows="3" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm outline-none focus:ring-2 resize-none placeholder-gray-300" placeholder="Tulis alasan penolakan secara jelas..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModals()" class="flex-1 py-3 border border-gray-200 text-gray-500 rounded-2xl text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-red-500 text-white rounded-2xl text-sm font-bold hover:bg-red-600 transition">Tolak Surat</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
function quickApprove(url, isSigner, detailUrl) {
    const formApprove = document.getElementById('formApprove');
    const signerWarning = document.getElementById('signerWarning');
    const btnGoToDetail = document.getElementById('btnGoToDetail');
    const approveModalTitle = document.getElementById('approveModalTitle');
    const approveModalSub = document.getElementById('approveModalSub');
    
    if (isSigner) {
        formApprove.classList.add('hidden');
        signerWarning.classList.remove('hidden');
        btnGoToDetail.href = detailUrl;
        approveModalTitle.textContent = 'Tanda Tangan Diperlukan';
        approveModalSub.textContent = 'Harap buka detail dokumen';
    } else {
        formApprove.action = url;
        formApprove.classList.remove('hidden');
        signerWarning.classList.add('hidden');
        approveModalTitle.textContent = 'Setujui Surat';
        approveModalSub.textContent = 'Konfirmasi persetujuan dokumen';
    }
    
    if (window.lucide) {
        window.lucide.createIcons();
    }
    
    const modal = document.getElementById('modalApprove');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function quickReject(url) {
    document.getElementById('formReject').action = url;
    const modal = document.getElementById('modalReject');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

function closeModals() {
    ['modalApprove', 'modalReject'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.classList.remove('flex');
    });
}

// Klik backdrop untuk tutup modal
['modalApprove', 'modalReject'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModals();
    });
});
</script>
@endpush

@endsection
