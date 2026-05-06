@extends('layouts.master')
@section('content')

    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-playfair text-[#1A2B24]">
          Approval Center
        </h1>
        <p class="text-xs text-gray-500">
          Review and approve employee requests
        </p>
      </div>
      @can('create', App\Models\Surat::class)
      <div class="shrink-0">
          <a href="{{ route('surat.create') }}" class="bg-[#4F6560] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-[#3d504c] transition shadow-sm">
              <i data-lucide="plus" class="w-4 h-4 inline-block mr-1"></i>
              Buat Surat Baru
          </a>
      </div>
      @endcan
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

    {{-- notifikasi approval — hanya tampil jika user punya jabatan (bukan staff) --}}
    @php
        $jabatanSaya = auth()->user()->profile?->jabatan;
        $myWaiting = 0;
        if ($jabatanSaya) {
            $myWaiting = \App\Models\DocumentApproval::where('status', 'waiting')
                ->where('jabatan', $jabatanSaya)
                ->where('document_type', 'LIKE', 'surat_%')
                ->count();
        }
    @endphp
    @if($myWaiting > 0)
    <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl"
         style="background:rgba(239,68,68,0.08); border-left:3px solid #ef4444;">
        <i data-lucide="bell-ring" class="w-4 h-4 text-red-500 flex-shrink-0"></i>
        <p class="text-sm text-red-700 font-medium">
            Ada <strong>{{ $myWaiting }} surat</strong> yang menunggu approval Anda.
        </p>
    </div>
    @endif

    <div class="space-y-5">

    @forelse($surats as $key => $surat)
        @php
            $jabatan = auth()->user()->profile?->jabatan;
            $waitingStep = $surat->approvals->where('status', 'waiting')->first();
            $isMyTurn = $jabatan
                && $waitingStep
                && $waitingStep->jabatan === $jabatan
                && !auth()->user()->hasRole('staff');
            $bisaApprove = $isMyTurn && $surat->status === 'submitted';
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white/80 backdrop-blur rounded-3xl px-6 py-5 shadow-sm border border-white/40 hover:shadow-md transition gap-4 {{ $isMyTurn ? 'border-l-4 border-l-red-400' : '' }}">
            
            <!-- left content (keep data same) -->
            <div class="flex items-center gap-4">
                <!-- avatar -->
                <div class="w-12 h-12 rounded-full bg-[#80BB9B]/30 flex items-center justify-center font-semibold text-[#4F6560] text-lg flex-shrink-0">
                    {{ strtoupper(substr($surat->user->name ?? 'U',0,1)) }}
                </div>

                <div>
                    <p class="font-semibold text-gray-800 text-base">
                        {{ $surat->user->name ?? 'Unknown' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }} | {{ $surat->nomor_surat }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $surat->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- right actions -->
            <div class="flex flex-col sm:items-end gap-3 sm:gap-2 border-t sm:border-0 pt-3 sm:pt-0 border-gray-100">
                <div>
                    @if($surat->status === 'submitted')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Diajukan</span>
                    @elseif($surat->status === 'approved_owner')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Disetujui Penuh</span>
                    @elseif($surat->status === 'rejected')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Ditolak</span>
                    @elseif($surat->status === 'revised')
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Perlu Revisi</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($surat->status) }}</span>
                    @endif
                </div>

                <div class="flex gap-2">
                    @can('view', $surat)
                    <a href="{{ route('surat.show', $surat->id) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-sm font-medium text-gray-600 transition">
                        Lihat
                    </a>
                    @endcan

                    @if($bisaApprove)
                    <button type="button"
                        onclick="quickApprove('{{ route('surat.approve', $surat->id) }}')"
                        class="px-4 py-2 bg-[#4F6560] text-white rounded-xl text-sm font-medium hover:bg-[#3d504c] transition shadow-sm">
                        <i data-lucide="check" class="w-4 h-4 inline-block mr-1"></i> Setuju
                    </button>
                    <button type="button"
                        onclick="quickReject('{{ route('surat.reject', $surat->id) }}')"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-300 transition">
                        <i data-lucide="x" class="w-4 h-4 inline-block mr-1"></i> Tolak
                    </button>
                    @endif
                </div>
            </div>
        </div>

    @empty
        <div class="bg-white/80 backdrop-blur rounded-3xl p-10 text-center shadow-sm border border-white/40">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
            <p class="text-gray-500 font-medium">Tidak ada data surat</p>
        </div>
    @endforelse

    </div>

    <div class="mt-6">
        {{ $surats->links() }}
    </div>

{{-- modal approve --}}
<div id="modalApprove" class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,.4);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6 overflow-y-auto max-h-[90vh]">
        <h6 class="text-base font-bold text-slate-900 mb-4">Setujui Surat</h6>
        <form id="formApprove" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan (opsional)</label>
                <textarea name="catatan" rows="2"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm"
                    placeholder="Tambahkan catatan jika ada..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    PIN Anda <span class="text-red-500">*</span>
                </label>
                <input type="password" name="pin" maxlength="6"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:border-custom-500 focus:ring-1 focus:ring-custom-100"
                    placeholder="Masukkan PIN 6 digit" required>
                <p class="text-xs text-slate-400 mt-1">PIN digunakan sebagai konfirmasi tanda tangan digital Anda</p>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModals()"
                    class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-custom-500 text-white rounded-lg text-sm font-bold">
                    <i data-lucide="shield-check" class="w-4 h-4 inline mr-1"></i>
                    Setujui dengan TTD
                </button>
            </div>
        </form>
    </div>
</div>

{{-- modal reject --}}
<div id="modalReject" class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,.4);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
        <h6 class="text-base font-bold text-slate-900 mb-4">Tolak Surat</h6>
        <form id="formReject" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-red-700 mb-1">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan_revisi" rows="3" required
                    class="w-full px-3 py-2 rounded-lg border border-red-200 text-sm"
                    placeholder="Tuliskan alasan penolakan secara jelas..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModals()"
                    class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-bold">
                    Tolak Surat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function quickApprove(url) {
    document.getElementById('formApprove').action = url;
    const modal = document.getElementById('modalApprove');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function quickReject(url) {
    document.getElementById('formReject').action = url;
    const modal = document.getElementById('modalReject');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
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