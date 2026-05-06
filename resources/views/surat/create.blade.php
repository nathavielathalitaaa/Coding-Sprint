@extends('layouts.master')
@section('content')
    
    <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-xl font-bold text-slate-900">Buat Surat Baru</h4>
                    <p class="text-sm text-slate-500 mt-0.5">Silakan isi formulir di bawah untuk mengajukan surat baru</p>
                </div>
            </div>

            <div class="hivi-hivi-card">
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg" style="background: rgba(239, 68, 68, 0.1); border-left: 3px solid #ef4444;">
                        <p class="text-sm font-semibold text-red-800 mb-2">Terjadi Kesalahan:</p>
                        <ul class="text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('surat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <div class="mb-6">
                                <label for="jenis_surat" class="block text-sm font-bold text-slate-700 mb-2">Jenis Surat <span class="text-red-500">*</span></label>
                                <select id="jenis_surat" name="jenis_surat" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-custom-500 focus:ring-2 focus:ring-custom-100 text-sm transition-all" required>
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    <option value="resign" @if(old('jenis_surat') === 'resign') selected @endif>Resign</option>
                                    <option value="permohonan" @if(old('jenis_surat') === 'permohonan') selected @endif>Permohonan</option>
                                    <option value="surat_tugas" @if(old('jenis_surat') === 'surat_tugas') selected @endif>Surat Tugas</option>
                                    <option value="rekomendasi" @if(old('jenis_surat') === 'rekomendasi') selected @endif>Rekomendasi</option>
                                    <option value="izin" @if(old('jenis_surat') === 'izin') selected @endif>Izin</option>
                                    <option value="lainnya" @if(old('jenis_surat') === 'lainnya') selected @endif>Lainnya</option>
                                </select>
                                @error('jenis_surat')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="perihal" class="block text-sm font-bold text-slate-700 mb-2">Perihal <span class="text-red-500">*</span></label>
                                <textarea id="perihal" name="perihal" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-custom-500 focus:ring-2 focus:ring-custom-100 text-sm transition-all" placeholder="Jelaskan perihal surat Anda" required>{{ old('perihal') }}</textarea>
                                @error('perihal')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="file_pdf" class="block text-sm font-bold text-slate-700 mb-2">File PDF <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="file" id="file_pdf" name="file_pdf" accept=".pdf" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-custom-500 focus:ring-2 focus:ring-custom-100 text-sm transition-all">
                                </div>
                                <p class="text-xs text-slate-500 mt-2">Format: PDF, Ukuran maksimal: 5MB</p>
                                @error('file_pdf')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror

                                <div id="ttd-mode-info" class="mt-4 p-4 rounded-xl border hidden">
                                    <p class="text-sm font-medium" id="ttd-mode-text"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <input type="hidden" name="ttd_coordinates" id="ttd_coordinates">

                            <div id="ttd-marker-section" class="hidden h-full">
                                <div class="p-6 bg-slate-50 dark:bg-zink-600 rounded-2xl border border-slate-200 dark:border-zink-500 h-full flex flex-col">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h6 class="text-sm font-bold text-slate-900 dark:text-zink-100">Posisi Tanda Tangan</h6>
                                            <p class="text-xs text-slate-500">Klik pada preview untuk menandai TTD</p>
                                        </div>
                                        <div id="marker-status" class="text-xs font-bold text-amber-600 px-3 py-1 bg-amber-50 rounded-full border border-amber-100">
                                            Belum ditandai
                                        </div>
                                    </div>

                                    <div id="approver-buttons" class="flex flex-wrap gap-2 mb-4">
                                        <!-- buttons generated by js -->
                                    </div>

                                    <div class="relative flex-1 bg-white border border-slate-200 rounded-xl overflow-hidden shadow-inner min-h-[400px]">
                                        <div id="pdf-container" class="relative w-full h-full overflow-auto text-center p-4 bg-slate-200">
                                            <div class="relative inline-block shadow-xl">
                                                <canvas id="pdf-canvas" class="block cursor-crosshair"></canvas>
                                                <div id="marker-layer" class="absolute inset-0 pointer-events-none"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-white/90 backdrop-blur px-3 py-2 rounded-full shadow-lg border border-slate-200">
                                            <button type="button" id="prev-page" class="p-1.5 hover:bg-slate-100 rounded-full transition-colors disabled:opacity-30">
                                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                            </button>
                                            <span class="text-xs font-bold text-slate-700 min-w-[80px] text-center">
                                                Hal <span id="current-page">1</span> / <span id="total-pages">1</span>
                                            </span>
                                            <button type="button" id="next-page" class="p-1.5 hover:bg-slate-100 rounded-full transition-colors disabled:opacity-30">
                                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('surat.index') }}" class="px-6 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50">Batal</a>
                        <button type="submit" class="hivi-btn-primary px-8">
                            Simpan & Ajukan Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    let pdfDoc = null;
    let pageNum = 1;
    let canvas = document.getElementById('pdf-canvas');
    let ctx = canvas.getContext('2d');
    let markerLayer = document.getElementById('marker-layer');
    
    let currentApprovers = [];
    let activeApproverIdx = 0;
    let coordinates = {}; 

    async function updateTtdMode() {
        const select = document.getElementById('jenis_surat');
        const jenis = select.value;
        if (!jenis) {
            document.getElementById('ttd-marker-section').classList.add('hidden');
            document.getElementById('ttd-mode-info').classList.add('hidden');
            return;
        }

        const resp = await fetch(`{{ route('surat.ttd-mode') }}?jenis_surat=${jenis}`);
        const data = await resp.json();

        const infoDiv = document.getElementById('ttd-mode-info');
        const infoText = document.getElementById('ttd-mode-text');

        if (data.mode === 'stamp') {
            document.getElementById('ttd-marker-section').classList.remove('hidden');
            infoDiv.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-100', 'text-emerald-700');
            infoDiv.classList.add('bg-blue-50', 'border-blue-100', 'text-blue-700');
            infoText.innerHTML = '<strong>Mode Stamp:</strong> Silakan tandai posisi tanda tangan pada preview PDF.';
            
            currentApprovers = data.approvers;
            activeApproverIdx = 0;
            renderApproverButtons();
            checkReady();
        } else {
            document.getElementById('ttd-marker-section').classList.add('hidden');
            infoDiv.classList.remove('hidden', 'bg-blue-50', 'border-blue-100', 'text-blue-700');
            infoDiv.classList.add('bg-emerald-50', 'border-emerald-100', 'text-emerald-700');
            infoText.innerHTML = '<strong>Mode Append:</strong> Lembar persetujuan TTD akan otomatis ditambahkan di akhir dokumen.';
        }
    }

    document.getElementById('jenis_surat').addEventListener('change', updateTtdMode);
    window.addEventListener('DOMContentLoaded', updateTtdMode);

    function renderApproverButtons() {
        const container = document.getElementById('approver-buttons');
        container.innerHTML = '';
        
        currentApprovers.forEach((app, idx) => {
            const isDone = coordinates[app.jabatan];
            const isActive = idx === activeApproverIdx;
            
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `px-3 py-1.5 rounded-lg text-xs font-bold transition-all border ${
                isActive ? 'bg-custom-500 text-white border-custom-500 shadow-sm' : 
                isDone ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 
                'bg-white text-slate-500 border-slate-200 hover:bg-slate-50'
            }`;
            btn.innerHTML = `${app.label} ${isDone ? '✓' : ''}`;
            btn.onclick = () => {
                activeApproverIdx = idx;
                renderApproverButtons();
                renderMarkers();
            };
            container.appendChild(btn);
        });
    }

    document.getElementById('file_pdf').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type === 'application/pdf') {
            const fileReader = new FileReader();
            fileReader.onload = function() {
                const typedarray = new Uint8Array(this.result);
                loadPDF(typedarray);
            };
            fileReader.readAsArrayBuffer(file);
        }
    });

    async function loadPDF(data) {
        pdfDoc = await pdfjsLib.getDocument(data).promise;
        document.getElementById('total-pages').textContent = pdfDoc.numPages;
        pageNum = 1;
        renderPage(pageNum);
    }

    async function renderPage(num) {
        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale: 1.2 });
        
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
        const renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };
        await page.render(renderContext).promise;
        
        document.getElementById('current-page').textContent = num;
        document.getElementById('prev-page').disabled = num <= 1;
        document.getElementById('next-page').disabled = num >= pdfDoc.numPages;
        
        renderMarkers();
    }

    document.getElementById('prev-page').onclick = () => {
        if (pageNum <= 1) return;
        pageNum--;
        renderPage(pageNum);
    };

    document.getElementById('next-page').onclick = () => {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        renderPage(pageNum);
    };

    canvas.onclick = function(e) {
        if (!currentApprovers[activeApproverIdx]) return;
        
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        const x = (e.clientX - rect.left) * scaleX;
        const y = (e.clientY - rect.top) * scaleY;
        
        const xPercent = (x / canvas.width) * 100;
        const yPercent = (y / canvas.height) * 100;
        
        const jabatan = currentApprovers[activeApproverIdx].jabatan;
        coordinates[jabatan] = {
            x: xPercent,
            y: yPercent,
            page: pageNum
        };
        
        if (activeApproverIdx < currentApprovers.length - 1) {
            activeApproverIdx++;
        }
        
        renderApproverButtons();
        renderMarkers();
        checkReady();
    };

    function renderMarkers() {
        markerLayer.innerHTML = '';
        Object.entries(coordinates).forEach(([jabatan, coord]) => {
            if (coord.page === pageNum) {
                const marker = document.createElement('div');
                const label = currentApprovers.find(a => a.jabatan === jabatan)?.label || jabatan;
                const isActive = currentApprovers[activeApproverIdx]?.jabatan === jabatan;
                
                marker.className = `absolute -translate-x-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center border-2 shadow-lg transition-all ${
                    isActive ? 'bg-custom-500 border-white text-white z-20 scale-110 font-bold' : 'bg-emerald-500 border-white text-white z-10'
                }`;
                marker.style.left = `${coord.x}%`;
                marker.style.top = `${coord.y}%`;
                marker.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                
                const labelEl = document.createElement('div');
                labelEl.className = 'absolute top-full mt-2 left-1/2 -translate-x-1/2 whitespace-nowrap bg-slate-800 text-white text-[10px] px-2 py-0.5 rounded shadow-sm font-bold';
                labelEl.textContent = label;
                marker.appendChild(labelEl);
                
                markerLayer.appendChild(marker);
            }
        });
        if (window.lucide) window.lucide.createIcons();
    }

    function checkReady() {
        const isAllMarked = currentApprovers.every(app => coordinates[app.jabatan]);
        const status = document.getElementById('marker-status');
        
        if (isAllMarked) {
            status.textContent = 'Siap ✓';
            status.className = 'text-xs font-bold text-emerald-600 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100';
            document.getElementById('ttd_coordinates').value = JSON.stringify(coordinates);
        } else {
            const count = Object.keys(coordinates).length;
            status.textContent = `${count}/${currentApprovers.length}`;
            status.className = 'text-xs font-bold text-amber-600 px-3 py-1 bg-amber-50 rounded-full border border-amber-100';
            document.getElementById('ttd_coordinates').value = '';
        }
    }
</script>
@endpush

