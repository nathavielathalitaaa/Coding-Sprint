@extends('layouts.master')
@section('content')
<style>
/* ── Tab System ── */
.abs-tab-bar {
    display: flex;
    gap: 0;
    border-bottom: 0.5px solid #F3F4F6;
    margin-bottom: 24px;
}
.abs-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 20px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 400;
    color: #6B7280;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
}
.abs-tab-btn.active {
    color: #1A2B24;
    font-weight: 500;
    border-bottom-color: #4F6560;
}
.abs-tab-btn:hover:not(.active) {
    color: #1A2B24;
}
.abs-tab-pane {
    display: none;
}
.abs-tab-pane.active {
    display: block;
}

/* ── Cards ── */
.abs-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    padding: 24px;
    margin-bottom: 20px;
}

/* ── Step Indicator ── */
.abs-steps {
    display: flex;
    align-items: center;
    margin-bottom: 28px;
}
.abs-step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    flex-shrink: 0;
}
.abs-step-circle.done { background: #80BB9B; color: #fff; }
.abs-step-circle.current { background: #4F6560; color: #fff; }
.abs-step-circle.pending { background: #F3F4F6; color: #6B7280; }
.abs-step-line {
    flex: 1;
    height: 1px;
    background: #E5E7EB;
    margin: 0 8px;
}
.abs-step-label {
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    color: #6B7280;
    text-align: center;
    margin-top: 4px;
}

/* ── Upload Zone ── */
.abs-dropzone {
    border: 2px dashed #D1E8DC;
    border-radius: 16px;
    background: #F6FAF8;
    padding: 40px 24px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
}
.abs-dropzone:hover, .abs-dropzone.dragover {
    background: #EDF4F0;
    border-color: #4F6560;
}

/* ── Warning box ── */
.abs-warning {
    background: #FEF3C7;
    border-left: 3px solid #F59E0B;
    border-radius: 0 10px 10px 0;
    padding: 12px 16px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin: 16px 0;
}

/* ── Summary counters ── */
.abs-summary-bar {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.abs-counter {
    background: #F6FAF8;
    border-radius: 12px;
    padding: 12px 16px;
    text-align: center;
}
.abs-counter-num {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
    color: #1A2B24;
}
.abs-counter-label {
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    color: #6B7280;
}

/* ── Review Table ── */
.abs-review-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.abs-review-table th {
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 500;
    color: #6B7280;
    padding: 10px 12px;
    border-bottom: 1px solid #E8EDED;
    text-align: left;
    white-space: nowrap;
}
.abs-review-table td {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    padding: 10px 12px;
    border-bottom: 1px solid #F3F4F6;
    vertical-align: middle;
}
.row-high { background: #fff; }
.row-medium { background: #FFFBEB; }
.row-low { background: #FFF5F5; }
.row-fixed { background: #fff; }

/* ── Confidence badges ── */
.badge-cocok { background: #E8F5EE; color: #0F6E56; border-radius: 9999px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
.badge-perlu { background: #FEF3C7; color: #92400E; border-radius: 9999px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
.badge-unknown { background: #FEE2E2; color: #991B1B; border-radius: 9999px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
.badge-fixed { background: #DBEAFE; color: #1E40AF; border-radius: 9999px; padding: 3px 10px; font-size: 11px; font-weight: 600; }

/* ── Import result ── */
.abs-result-card {
    background: #E8F5EE;
    border: 1px solid #80BB9B;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
}
.abs-result-num {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    font-weight: 700;
    color: #1A2B24;
}

/* ── Rekap stats ── */
.abs-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin: 20px 0;
}
.abs-stat-card {
    background: #F6FAF8;
    border-radius: 14px;
    padding: 16px;
}
.abs-stat-val {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-weight: 700;
    color: #1A2B24;
}
.abs-stat-lbl {
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    color: #6B7280;
    margin-top: 2px;
}

@media(max-width: 768px) {
    .abs-stat-grid { grid-template-columns: repeat(2, 1fr); }
    .abs-summary-bar { grid-template-columns: 1fr; }
}
</style>

<div class="mb-8">
  <h1 class="text-3xl font-playfair font-bold text-[#1A2B24]">Attendance Management</h1>
  <p class="text-[13px] font-light text-[#6B7280] mt-1">Manage all Sinergi Hotel employee attendance records</p>
</div>

<!-- TAB BAR -->
<div class="abs-tab-bar">
  <button class="abs-tab-btn active" data-tab="tab1" id="btn-tab1">
    <i data-lucide="calendar-check" style="width:15px;height:15px;"></i> Attendance Data
  </button>
  <button class="abs-tab-btn" data-tab="tab2" id="btn-tab2">
    <i data-lucide="upload-cloud" style="width:15px;height:15px;"></i> Import Fingerprint
  </button>
  <button class="abs-tab-btn" data-tab="tab3" id="btn-tab3">
    <i data-lucide="bar-chart-2" style="width:15px;height:15px;"></i> Summary &amp; Reports
  </button>
</div>

<!-- ══════════ TAB 1 — DATA ABSENSI ══════════ -->
<div class="abs-tab-pane active" id="tab1">
  <div class="abs-card">
    
    <!-- Filter bar -->
    <div class="flex flex-wrap items-center gap-3 mb-5">
      <form method="GET" action="{{ route('hr/absensi/page') }}" class="flex flex-wrap items-center gap-3">
        <input type="month" name="bulan" value="{{ $bulan }}" class="hivi-input w-auto" onchange="this.form.submit()">
      </form>
      <input type="text" id="tab1-search" class="hivi-input w-56" placeholder="Search employee name..." oninput="filterTab1(this.value)">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="hivi-table w-full" id="tab1-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Employee Name</th>
            <th>Department</th>
            <th>Required Days</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Late</th>
            <th>Overtime</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rekapList as $key => $absensi)
            @php 
              $d = $absensi->rekap; 
              $hadir = $d['hari_hadir'] ?? 0; 
              $dibut = $d['hari_dibutuhkan'] ?? 0; 
              $pct = $dibut > 0 ? round($hadir / $dibut * 100) : 0; 
            @endphp
            <tr class="tab1-row">
              <td>{{ $key + 1 }}</td>
              <td class="font-medium text-slate-800">{{ $absensi->user?->name ?? '-' }}</td>
              <td>{{ $absensi->user?->department ?? ($absensi->user?->profile?->jabatan ?? '-') }}</td>
              <td>{{ $dibut }}</td>
              <td>
                <span class="hivi-badge hivi-badge-green">{{ $hadir }} hari</span>
              </td>
              <td>
                <span class="hivi-badge {{ ($d['hari_tidak_hadir'] ?? 0) > 0 ? 'hivi-badge-red' : 'hivi-badge-gray' }}">
                  {{ $d['hari_tidak_hadir'] ?? 0 }} hari
                </span>
              </td>
              <td>
                <span class="hivi-badge {{ ($d['terlambat_count'] ?? 0) > 0 ? 'hivi-badge-amber' : 'hivi-badge-gray' }}">
                  {{ $d['terlambat_count'] ?? 0 }}x / {{ $d['terlambat_menit'] ?? 0 }}mnt
                </span>
              </td>
              <td>
                <span class="hivi-badge hivi-badge-blue">
                  {{ isset($d['lembur_menit']) ? round($d['lembur_menit'] / 60, 1) : 0 }} jam
                </span>
              </td>
              <td>
                <button onclick="deleteAbsensi({{ $absensi->id }})" 
                        class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors" 
                        title="Delete">
                  <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-slate-500 py-8">
                No attendance data for this month.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- ══════════ TAB 2 — IMPORT FINGERPRINT ══════════ -->
<div class="abs-tab-pane" id="tab2">
  
  <!-- Step Indicator -->
  <div class="abs-steps" id="step-indicator">
    <div class="flex flex-col items-center">
      <div class="abs-step-circle current" id="s1-circle">1</div>
      <div class="abs-step-label">Upload</div>
    </div>
    <div class="abs-step-line"></div>
    <div class="flex flex-col items-center">
      <div class="abs-step-circle pending" id="s2-circle">2</div>
      <div class="abs-step-label">Review</div>
    </div>
    <div class="abs-step-line"></div>
    <div class="flex flex-col items-center">
      <div class="abs-step-circle pending" id="s3-circle">3</div>
      <div class="abs-step-label">Hasil</div>
    </div>
  </div>

  <!-- STEP 1: Upload -->
  <div id="step1" class="abs-card">
    <div class="abs-dropzone" id="ai-dropzone" onclick="document.getElementById('ai-file').click()">
      <i data-lucide="upload-cloud" class="w-10 h-10 text-custom-500 mb-3 mx-auto"></i>
      <p class="font-medium text-sm mb-1 font-poppins">Upload File Fingerprint</p>
      <p class="font-light text-xs text-slate-500 font-poppins m-0">Format .xlsx atau .xls &middot; Maksimal 5MB</p>
      <input type="file" id="ai-file" accept=".xlsx,.xls" class="hidden" onchange="handleAiFile(this)">
    </div>
    
    <div id="ai-file-info" class="hidden items-center gap-3 mt-3 p-3 bg-white border border-slate-200 rounded-xl">
      <i data-lucide="file-spreadsheet" class="w-5 h-5 text-custom-500"></i>
      <div>
        <div id="ai-file-name" class="text-sm font-semibold text-slate-800"></div>
        <div id="ai-file-size" class="text-xs text-slate-500"></div>
      </div>
    </div>
    
    <div class="abs-warning">
      <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-700 flex-shrink-0 mt-0.5"></i>
      <span class="font-poppins text-xs text-amber-700">
        Sistem akan mencocokkan nama di file fingerprint dengan data karyawan menggunakan AI. 
        Pastikan data karyawan di Staff Directory sudah lengkap sebelum import.
      </span>
    </div>
    
    <button id="ai-process-btn" 
            onclick="processWithAI()" 
            disabled 
            class="w-full p-3.5 bg-slate-400 text-white rounded-full font-poppins text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-2 transition-colors duration-200 mt-4">
      <i data-lucide="upload-cloud" class="w-4 h-4" id="ai-btn-icon"></i>
      <span id="ai-btn-text">Pilih file terlebih dahulu...</span>
    </button>
  </div>

  <!-- STEP 2: Review -->
  <div id="step2" style="display:none;">
    <div class="abs-summary-bar" id="ai-summary-bar">
      <div class="abs-counter">
        <div class="abs-counter-num" id="cnt-cocok">0</div>
        <div class="abs-counter-label">✓ Cocok otomatis</div>
      </div>
      <div class="abs-counter">
        <div class="abs-counter-num" id="cnt-perlu">0</div>
        <div class="abs-counter-label">⚠ Perlu konfirmasi</div>
      </div>
      <div class="abs-counter">
        <div class="abs-counter-num" id="cnt-unknown">0</div>
        <div class="abs-counter-label">✗ Tidak dikenal</div>
      </div>
    </div>
    
    <div class="abs-card overflow-x-auto">
      <table class="abs-review-table" id="ai-review-table">
        <thead>
          <tr>
            <th><input type="checkbox" id="check-all" onchange="toggleAllHigh(this)"></th>
            <th>Nama di Fingerprint</th>
            <th>Nama Karyawan</th>
            <th>Confidence</th>
            <th>Hadir</th>
            <th>Tidak Hadir</th>
            <th>Terlambat</th>
            <th>Pilihan</th>
          </tr>
        </thead>
        <tbody id="ai-review-body"></tbody>
      </table>
    </div>
    
    <div class="flex flex-wrap gap-3 mt-4">
      <button onclick="confirmAllHigh()" class="hivi-btn-secondary text-sm px-4 py-2">
        Konfirmasi Semua yang Cocok
      </button>
      <button onclick="saveConfirmed()" class="hivi-btn-primary text-sm px-4 py-2 flex items-center gap-2">
        <i data-lucide="save" class="w-4 h-4"></i> Simpan Data Terkonfirmasi
      </button>
    </div>
  </div>

  <!-- STEP 3: Result -->
  <div id="step3" style="display:none;">
    <div class="abs-result-card">
      <i data-lucide="check-circle" class="w-12 h-12 text-emerald-600 mb-3 mx-auto"></i>
      <div class="abs-result-num" id="res-imported">0</div>
      <div class="font-poppins text-sm text-slate-800 my-1">karyawan berhasil diimpor</div>
      <div id="res-skipped-text" class="font-poppins text-xs font-light text-slate-500 mt-1"></div>
    </div>
    
    <div id="skipped-list" class="mt-4 hidden">
      <details>
        <summary class="cursor-pointer font-poppins text-sm text-slate-500">Lihat baris yang dilewati ▼</summary>
        <ul id="skipped-names" class="mt-2 pl-5 font-poppins text-sm text-slate-500 list-disc"></ul>
      </details>
    </div>
    
    <div class="mt-5 text-center">
      <button onclick="switchTab('tab1')" class="hivi-btn-primary inline-flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Data Absensi
      </button>
    </div>
  </div>
</div>
<!-- ══════════ TAB 3 — REKAP & LAPORAN ══════════ -->
<div class="abs-tab-pane" id="tab3">
  
  <!-- Filter bar -->
  <div class="flex flex-wrap items-center gap-3 mb-5">
    <input type="month" id="r-bulan" value="{{ $bulan }}" class="hivi-input w-auto" onchange="loadRekap()">
    <select id="r-dept" class="hivi-input w-auto" onchange="loadRekap()">
      <option value="">Semua Departemen</option>
      @foreach($departments as $dept)
        <option value="{{ $dept }}">{{ $dept }}</option>
      @endforeach
    </select>
    
    <div class="ml-auto flex gap-2">
      <button onclick="exportRekap('excel')" class="hivi-btn-outline inline-flex items-center gap-2 text-sm">
        <i data-lucide="download" class="w-4 h-4"></i> Excel
      </button>
      <button onclick="exportRekap('pdf')" class="hivi-btn-outline inline-flex items-center gap-2 text-sm">
        <i data-lucide="file-text" class="w-4 h-4"></i> PDF
      </button>
    </div>
  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="abs-card md:col-span-2">
      <h2 class="font-playfair text-lg font-semibold text-slate-800 mb-4">Kehadiran Harian</h2>
      <div class="w-full h-56 relative">
        <canvas id="chart-daily"></canvas>
      </div>
    </div>
    
    <div class="abs-card md:col-span-1">
      <h2 class="font-playfair text-lg font-semibold text-slate-800 mb-4">Distribusi Status</h2>
      <div class="relative h-56 flex items-center justify-center">
        <canvas id="chart-donut"></canvas>
        <div id="donut-center" class="absolute text-center pointer-events-none">
          <div class="font-playfair text-2xl font-bold text-slate-800" id="donut-total">-</div>
          <div class="font-poppins text-xs text-slate-500">hari kerja</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stat cards -->
  <div class="abs-stat-grid">
    <div class="abs-stat-card">
      <div class="abs-stat-val" id="s-hadir">-</div>
      <div class="abs-stat-lbl">Total Hadir</div>
    </div>
    <div class="abs-stat-card">
      <div class="abs-stat-val" id="s-izin">-</div>
      <div class="abs-stat-lbl">Total Izin</div>
    </div>
    <div class="abs-stat-card">
      <div class="abs-stat-val" id="s-alpha">-</div>
      <div class="abs-stat-lbl">Total Alpha</div>
    </div>
    <div class="abs-stat-card">
      <div class="abs-stat-val" id="s-avg">-</div>
      <div class="abs-stat-lbl">Rata-rata Kehadiran</div>
    </div>
  </div>

  <!-- Rekap table -->
  <div class="abs-card overflow-x-auto">
    <table class="hivi-table w-full" id="rekap-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Departemen</th>
          <th>Hadir</th>
          <th>Izin</th>
          <th>Alpha</th>
          <th>Terlambat</th>
          <th>% Kehadiran</th>
        </tr>
      </thead>
      <tbody id="rekap-body">
        <tr>
          <td colspan="8" class="text-center text-slate-500 py-6">Loading data...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── All employees for manual override dropdown (prepared in controller)
const allEmployees = @json($allEmployees);

const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let aiRows = [];          // matched rows from Groq
let chartDaily = null;
let chartDonut = null;

/* ══ TAB SWITCHING ══ */
function switchTab(tabId) {
    document.querySelectorAll('.abs-tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.abs-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    if (tabId === 'tab3') loadRekap();
    lucide.createIcons();
}
document.querySelectorAll('.abs-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.dataset.tab));
});

/* ══ TAB 1: Filter ══ */
function filterTab1(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.tab1-row').forEach(tr => {
        const name = tr.cells[1].textContent.toLowerCase();
        tr.style.display = name.includes(q) ? '' : 'none';
    });
}
function deleteAbsensi(id) {
    if (!confirm('Hapus data absensi ini?')) return;
    fetch('/hr/absensi/' + id, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    }).then(() => location.reload());
}

/* ══ TAB 2: AI Upload ══ */
let selectedFile = null;

function handleAiFile(input) {
    const file = input.files ? input.files[0] : input;
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['xls','xlsx'].includes(ext)) { alert('Format harus .xls atau .xlsx'); return; }
    selectedFile = file;
    document.getElementById('ai-file-name').textContent = file.name;
    document.getElementById('ai-file-size').textContent = file.size < 1048576 ? (file.size/1024).toFixed(1)+' KB' : (file.size/1048576).toFixed(1)+' MB';
    const card = document.getElementById('ai-file-info');
    card.style.display = 'flex';
    const btn = document.getElementById('ai-process-btn');
    btn.disabled = false;
    btn.style.background = '#4F6560';
    btn.style.cursor = 'pointer';
    document.getElementById('ai-btn-text').textContent = 'Proses dengan AI';
    lucide.createIcons();
}

async function processWithAI() {
    if (!selectedFile) return;
    const btn = document.getElementById('ai-process-btn');
    btn.disabled = true;
    btn.style.background = '#9CA3AF';
    document.getElementById('ai-btn-text').textContent = 'AI sedang menganalisis...';
    const fd = new FormData();
    fd.append('file', selectedFile);
    fd.append('_token', CSRF);
    try {
        const res = await fetch('/hr/absensi/process-ai', {method:'POST', body:fd});
        const data = await res.json();
        if (!data.success) { alert('Error: ' + (data.error || 'Terjadi kesalahan')); resetUpload(); return; }
        aiRows = data.rows;
        renderReview(data.rows, data.periode);
    } catch(e) { alert('Network error: ' + e.message); resetUpload(); }
}
function resetUpload() {
    const btn = document.getElementById('ai-process-btn');
    btn.disabled = false; btn.style.background = '#4F6560'; btn.style.cursor = 'pointer';
    document.getElementById('ai-btn-text').textContent = 'Proses dengan AI';
}

function renderReview(rows, periode) {
    let cocok=0, perlu=0, unknown=0;
    const tbody = document.getElementById('ai-review-body');
    tbody.innerHTML = '';
    rows.forEach((r, i) => {
        const conf = r.confidence || 0;
        let lvl = conf >= 85 ? 'high' : (conf >= 60 ? 'medium' : 'low');
        if (lvl === 'high') cocok++; else if (lvl === 'medium') perlu++; else unknown++;
        const rowClass = lvl==='high'?'row-high':(lvl==='medium'?'row-medium':'row-low');
        const badge = lvl==='high'?`<span class="badge-cocok">Cocok</span>`:(lvl==='medium'?`<span class="badge-perlu">Perlu Cek</span>`:`<span class="badge-unknown">Tidak Dikenal</span>`);
        let aksiHtml = '';
        if (lvl === 'high') {
            aksiHtml = `<input type="checkbox" class="row-check" data-idx="${i}" checked title="Konfirmasi otomatis">`;
        } else if (lvl === 'medium') {
            const opts = (r.alternatives||[]).map(a => `<option value="${a.user_id}">${a.name} (${a.confidence}%)</option>`).join('');
            aksiHtml = `<select class="hivi-input" style="width:180px;font-size:12px;padding:6px 12px;" onchange="manualOverride(${i}, this.value, this.options[this.selectedIndex].text)">
                <option value="">Pilih karyawan...</option>
                ${opts}
                <option value="__manual__">Pilih manual...</option>
                <option value="__skip__">⊘ Lewati baris ini</option>
            </select>`;
        } else {
            aksiHtml = buildEmployeeSelect(i);
        }
        tbody.innerHTML += `<tr class="${rowClass}" id="row-${i}">
            <td>${lvl==='high'?`<input type="checkbox" class="row-check" data-idx="${i}" checked>`:'&nbsp;'}</td>
            <td><strong>${r.nama}</strong></td>
            <td id="matched-${i}">${r.matched_name||'<span style="color:#9CA3AF;">—</span>'}</td>
            <td>${badge} <small style="color:#6B7280;">${conf}%</small></td>
            <td>${r.hari_hadir??0}</td><td>${r.hari_tidak_hadir??0}</td>
            <td>${r.terlambat_count??0}x</td>
            <td id="aksi-${i}">${aksiHtml}</td>
        </tr>`;
    });
    document.getElementById('cnt-cocok').textContent = cocok;
    document.getElementById('cnt-perlu').textContent = perlu;
    document.getElementById('cnt-unknown').textContent = unknown;
    // Update steps
    setStep(2);
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
    lucide.createIcons();
}

function buildEmployeeSelect(idx) {
    const opts = allEmployees.map(e => `<option value="${e.id}">${e.name}${e.dept?' — '+e.dept:''}</option>`).join('');
    return `<div style="display:flex;flex-direction:column;gap:4px;">
        <select class="hivi-input" style="width:200px;font-size:12px;padding:6px 12px;" onchange="manualOverride(${idx}, this.value, this.options[this.selectedIndex].text)">
            <option value="">Pilih karyawan...</option>${opts}
            <option value="__skip__">⊘ Lewati baris ini</option>
        </select>
    </div>`;
}

function manualOverride(idx, userId, name) {
    if (userId === '__skip__') {
        aiRows[idx]._skip = true;
        aiRows[idx].user_id = null;
        document.getElementById('row-'+idx).className = 'row-low';
        return;
    }
    if (userId === '__manual__') {
        document.getElementById('aksi-'+idx).innerHTML = buildEmployeeSelect(idx);
        lucide.createIcons(); return;
    }
    aiRows[idx].user_id = parseInt(userId);
    aiRows[idx]._skip = false;
    document.getElementById('row-'+idx).className = 'row-fixed';
    document.getElementById('matched-'+idx).innerHTML = name.split('—')[0].trim();
    const cell = document.getElementById('aksi-'+idx);
    cell.innerHTML = `<span class="badge-fixed">Diperbaiki</span>`;
}

function toggleAllHigh(cb) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked);
}
function confirmAllHigh() {
    document.querySelectorAll('.row-check').forEach(c => { c.checked = true; });
    alert('Semua baris "Cocok" telah dikonfirmasi.');
}

async function saveConfirmed() {
    const rows = aiRows.map((r, i) => {
        const cb = document.querySelector(`.row-check[data-idx="${i}"]`);
        const skip = r._skip || (cb && !cb.checked);
        return { ...r, skip: !!skip };
    });
    const periode = document.querySelector('#ai-review-body tr') ? 'Import ' + new Date().toLocaleDateString('id-ID',{month:'long',year:'numeric'}) : '';
    try {
        const res = await fetch('/hr/absensi/confirm-import', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body: JSON.stringify({ rows, periode })
        });
        const data = await res.json();
        if (!data.success) { alert('Gagal menyimpan: ' + (data.error||'error')); return; }
        document.getElementById('res-imported').textContent = data.imported;
        if (data.skipped > 0) {
            document.getElementById('res-skipped-text').textContent = data.skipped + ' baris dilewati';
            document.getElementById('skipped-list').style.display = 'block';
        }
        setStep(3);
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
        lucide.createIcons();
    } catch(e) { alert('Network error: ' + e.message); }
}

function setStep(n) {
    ['s1','s2','s3'].forEach((s,i) => {
        const el = document.getElementById(s+'-circle');
        if (!el) return;
        const step = i+1;
        el.className = 'abs-step-circle ' + (step < n ? 'done' : step === n ? 'current' : 'pending');
        el.textContent = step < n ? '✓' : step;
    });
}

/* ══ TAB 3: Rekap & Charts ══ */
async function loadRekap() {
    const bulan = document.getElementById('r-bulan').value;
    const dept  = document.getElementById('r-dept').value;
    const url   = `/hr/absensi/rekap-data?bulan=${bulan}&departemen=${encodeURIComponent(dept)}`;
    try {
        const res  = await fetch(url, {headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});
        const data = await res.json();
        if (!data.success) return;
        // Stat cards
        document.getElementById('s-hadir').textContent = data.totals.hadir;
        document.getElementById('s-izin').textContent  = data.totals.izin;
        document.getElementById('s-alpha').textContent = data.totals.alpha;
        document.getElementById('s-avg').textContent   = data.totals.avg_pct + '%';
        // Daily bar chart
        const labels = Array.from({length: data.days_in_month}, (_,i) => i+1);
        const daily  = data.daily;
        if (chartDaily) chartDaily.destroy();
        chartDaily = new Chart(document.getElementById('chart-daily'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {label:'Hadir', data: daily.map(d=>d.hadir), backgroundColor:'#80BB9B', borderRadius:6},
                    {label:'Tidak Hadir', data: daily.map(d=>d.alpha), backgroundColor:'#E57373', borderRadius:6},
                    {label:'Izin', data: daily.map(d=>d.izin), backgroundColor:'#F59E0B', borderRadius:6},
                ]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom',labels:{font:{family:'Poppins',size:11}}}},
                scales:{
                    x:{grid:{display:false},ticks:{font:{family:'Poppins',size:10}}},
                    y:{grid:{color:'#F3F4F6'},ticks:{font:{family:'Poppins',size:10}}}
                }
            }
        });
        // Donut
        const dist = data.distribution;
        const total = dist.hadir + dist.izin + dist.alpha + dist.terlambat;
        document.getElementById('donut-total').textContent = total;
        if (chartDonut) chartDonut.destroy();
        chartDonut = new Chart(document.getElementById('chart-donut'), {
            type: 'doughnut',
            data: {
                labels:['Hadir','Izin','Alpha','Terlambat'],
                datasets:[{data:[dist.hadir,dist.izin,dist.alpha,dist.terlambat],backgroundColor:['#80BB9B','#F59E0B','#E57373','#4F6560'],borderWidth:0}]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                cutout:'72%',
                plugins:{legend:{position:'right',labels:{font:{family:'Poppins',size:11}}}}
            }
        });
        // Employee table
        const tbody = document.getElementById('rekap-body');
        tbody.innerHTML = '';
        data.employees.forEach((e,i) => {
            const pctColor = e.pct>=90?'#2E7D5E':(e.pct>=70?'#92400E':'#991B1B');
            tbody.innerHTML += `<tr style="cursor:default;" onmouseover="this.style.background='#F0F7F3'" onmouseout="this.style.background=''">
                <td>${i+1}</td><td style="font-weight:500;">${e.nama}</td><td>${e.departemen}</td>
                <td>${e.hadir}</td><td>${e.izin}</td><td>${e.alpha}</td><td>${e.terlambat}x</td>
                <td style="font-weight:600;color:${pctColor};">${e.pct}%</td>
            </tr>`;
        });
        if (!data.employees.length) tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#6B7280;padding:24px;">Belum ada data rekap bulan ini.</td></tr>';
    } catch(e) { console.error('Rekap error:', e); }
}

function exportRekap(type) {
    const bulan = document.getElementById('r-bulan').value;
    const url = type === 'excel'
        ? `/hr/absensi/export/excel?bulan=${bulan}`
        : `/hr/absensi/export/pdf?bulan=${bulan}`;
    window.location.href = url;
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    // Dropzone drag & drop events (must be inside DOMContentLoaded)
    const aiDropzone = document.getElementById('ai-dropzone');
    if (aiDropzone) {
        ['dragenter','dragover'].forEach(e => aiDropzone.addEventListener(e, ev => { ev.preventDefault(); aiDropzone.classList.add('dragover'); }));
        ['dragleave','drop'].forEach(e => aiDropzone.addEventListener(e, ev => { ev.preventDefault(); aiDropzone.classList.remove('dragover'); }));
        aiDropzone.addEventListener('drop', ev => {
            const f = ev.dataTransfer.files[0];
            if (f) { document.getElementById('ai-file').files = ev.dataTransfer.files; handleAiFile({files:[f]}); }
        });
    }
    // Skeleton → real content (Tab 1)
    setTimeout(() => {
        document.querySelectorAll('.skeleton-wrapper').forEach(el => { el.style.opacity='0'; setTimeout(()=>el.style.display='none',200); });
        document.querySelectorAll('.real-content').forEach(el => { el.classList.remove('hidden'); void el.offsetWidth; el.classList.add('loaded'); });
    }, 400);
});

</script>
@endpush
@endsection