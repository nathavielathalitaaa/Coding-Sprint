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
        <h1 class="text-3xl font-sans font-bold text-[#111111]">Inbox Admin Sekretariat</h1>
        <p class="text-[13px] font-light text-[#6B7280] mt-1">Review format dan registrasi nomor surat</p>
      </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 px-4 py-3 relative text-base text-green-800 bg-green-50 rounded-[28px]" role="alert">
            {{ $message }}
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="mb-4 px-4 py-3 relative text-base text-red-800 bg-red-50 rounded-[28px]" role="alert">
            {{ $message }}
        </div>
    @endif


    <div class="w-full">
        <div class="skeleton-wrapper w-full">
            <div class="space-y-4">
                @for($i=0; $i<4; $i++)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white rounded-[28px] shadow-[0_10px_40px_rgba(0,0,0,0.06)] border border-gray-100 px-6 py-5 gap-4">
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

    @forelse($surats as $key => $surat)

        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white rounded-[28px] shadow-sm border border-gray-100 hover:shadow-md transition px-6 py-5 gap-4">
            
            <!-- left content (keep data same) -->
            <div class="flex items-center gap-4">
                <!-- avatar -->
                <div class="w-12 h-12 rounded-full bg-[var(--color-bg-light)] flex items-center justify-center font-semibold text-[var(--color-text)] text-lg flex-shrink-0">
                    {{ strtoupper(substr($surat->user->name ?? 'U',0,1)) }}
                </div>

                <div>
                    <p class="font-semibold text-[var(--color-text)] text-base">
                        {{ $surat->user->name ?? 'Unknown' }}
                    </p>
                    <p class="text-sm text-[var(--color-text-muted)] mt-0.5">
                        {{ $surat->suratType ? $surat->suratType->nama : ucfirst(str_replace('_', ' ', $surat->jenis_surat)) }} 
                        @if($surat->organisasi)
                            &bull; <span class="text-[var(--color-text)] font-medium">{{ $surat->organisasi->nama }}</span>
                        @endif
                        <br>
                        <span class="text-xs">Menunggu Nomor Surat</span>
                    </p>
                    <p class="text-xs text-[var(--color-text-muted)] mt-1">
                        Diajukan pada: {{ $surat->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- right actions -->
            <div class="flex flex-col sm:items-end gap-3 sm:gap-2 border-t sm:border-0 pt-3 sm:pt-0 border-gray-100">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-[var(--color-bg-light)] text-[var(--color-primary)]">Perlu Verifikasi</span>
                </div>

                <div class="surat-actions flex flex-col sm:flex-row gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                    <a href="{{ route('surat.show', $surat->id) }}"
                       class="px-4 py-2 rounded-2xl border border-[var(--color-border)] bg-white text-[var(--color-text)] text-sm font-medium hover:bg-[var(--color-bg-light)] transition flex items-center justify-center">
                        Lihat Dokumen
                    </a>

                    <button type="button"
                        onclick="quickVerify('{{ route('surat.verifikasi_admin', $surat->id) }}', '{{ $surat->suratType ? app(\App\Services\SuratNumberService::class)->previewNext($surat->suratType) : '(format belum dikonfigurasi)' }}', '{{ addslashes($surat->suratType?->nama ?? ucfirst(str_replace('_',' ',$surat->jenis_surat))) }}')"
                        class="px-4 py-2 bg-[var(--color-primary)] text-white rounded-2xl text-sm font-medium hover:bg-[var(--color-primary-dark)] transition shadow-sm flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Disposisi
                    </button>
                    <button type="button"
                        onclick="quickRejectAdmin('{{ route('surat.verifikasi_admin', $surat->id) }}')"
                        class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-2xl text-sm font-medium hover:bg-red-100 transition flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4 mr-1"></i> Kembalikan
                    </button>
                </div>
            </div>
        </div>

    @empty
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.06)] p-16 text-center">
            <div class="w-16 h-16 bg-[var(--color-bg-light)] rounded-[28px] flex items-center justify-center mx-auto mb-4">
                <i data-lucide="file-text" class="w-8 h-8 text-[var(--color-primary)]"></i>
            </div>
            <h3 class="text-lg font-semibold text-[var(--color-text)] mb-2">Belum Ada Surat</h3>
            <p class="text-sm text-[var(--color-text-muted)] mb-6">Ajukan surat baru untuk memulai alur persetujuan.</p>
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

{{-- modal verifikasi --}}
<div id="modalVerifikasi" class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,.4);">
    <div class="bg-white rounded-[28px] shadow-xl w-full max-w-md mx-4 p-6 overflow-y-auto max-h-[90vh]">

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <div>
                <h6 class="text-base font-bold text-slate-900">Verifikasi & Disposisi Awal</h6>
                <p id="modalJenisSurat" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
        </div>

        <form id="formVerifikasi" method="POST">
            @csrf
            <input type="hidden" name="action" value="approve">

            {{-- Preview nomor — read-only, tidak ada input --}}
            <div class="mb-5 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">
                    Nomor yang akan diberikan
                </p>
                <p id="modalPreviewNomor"
                   class="text-lg font-mono font-bold text-slate-800 tracking-wide break-all">
                    —
                </p>
                <p class="text-[11px] text-slate-400 mt-2 leading-relaxed">
                    Nomor ini digenerate otomatis berdasarkan format dan counter jenis surat.
                    Tidak dapat diubah manual.
                </p>
            </div>

            <div class="flex gap-3 justify-end pt-1">
                <button type="button" onclick="closeModals()"
                    class="px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-[var(--color-primary)] text-white rounded-xl text-sm font-bold hover:bg-[var(--color-primary-dark)] transition flex items-center gap-2 shadow-sm">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Verifikasi & Teruskan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- modal reject --}}
<div id="modalReject" class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,.4);">
    <div class="bg-white rounded-[28px] shadow-xl w-full max-w-md mx-4 p-6">
        <h6 class="text-base font-bold text-slate-900 mb-4">Kembalikan Dokumen</h6>
        <form id="formReject" method="POST">
            @csrf
            <input type="hidden" name="action" value="reject">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-red-700 mb-1">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan_revisi" rows="3" required
                    class="w-full px-3 py-2 rounded-lg border border-red-200 text-sm"
                    placeholder="Tulis alasan penolakan secara jelas..."></textarea>
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
    function quickVerify(url, previewNomor, jenisSurat) {
        const form = document.getElementById('formVerifikasi');
        form.action = url;

        // Tampilkan preview nomor dan jenis surat di modal
        const elNomor = document.getElementById('modalPreviewNomor');
        const elJenis = document.getElementById('modalJenisSurat');
        if (elNomor) elNomor.textContent = previewNomor || '—';
        if (elJenis) elJenis.textContent  = jenisSurat  || '';

        document.getElementById('modalVerifikasi').classList.remove('hidden');
        document.getElementById('modalVerifikasi').classList.add('flex');
    }

    function quickRejectAdmin(url) {
        const form = document.getElementById('formReject');
        form.action = url;
        document.getElementById('modalReject').classList.remove('hidden');
        document.getElementById('modalReject').classList.add('flex');
    }

    function closeModals() {
        document.getElementById('modalVerifikasi').classList.add('hidden');
        document.getElementById('modalVerifikasi').classList.remove('flex');
        document.getElementById('modalReject').classList.add('hidden');
        document.getElementById('modalReject').classList.remove('flex');
        
        document.getElementById('formVerifikasi').reset();
        document.getElementById('formReject').reset();
    }

    window.onclick = function(event) {
        const mv = document.getElementById('modalVerifikasi');
        const mr = document.getElementById('modalReject');
        if (event.target == mv || event.target == mr) {
            closeModals();
        }
    }
</script>
@endpush

@endsection

