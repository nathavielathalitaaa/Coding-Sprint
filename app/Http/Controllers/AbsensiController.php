<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AbsensiController extends Controller
{
    // ══════════════════════════════════════════════════════
    // TAB 1 — DATA ABSENSI
    // ══════════════════════════════════════════════════════

    /**
     * Tampilkan halaman utama dengan Tab 1 data.
     * Hanya HR yang bisa akses halaman ini.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('BPH')) {
            abort(403, 'Akses ditolak.');
        }

        $bulan = $request->bulan ?? date('Y-m');
        $tanggalMulai = $bulan . '-01';

        $query = Absensi::with(['user.profile', 'user' => function ($q) {
            $q->select(['id', 'name', 'department']);
        }])->where('tanggal', $tanggalMulai);

        // Filter departemen
        if ($request->departemen) {
            $query->whereHas('user', fn($q) => $q->where('department', $request->departemen));
        }

        $absensiList = $query->orderBy('user_id')->get();

        // Map rekap JSON
        $rekapList = $absensiList->map(function ($item) {
            $data = json_decode($item->keterangan, true);
            if (!is_array($data)) {
                $data = [
                    'periode'          => '-',
                    'hari_dibutuhkan'  => 0,
                    'hari_hadir'       => 0,
                    'hari_tidak_hadir' => 0,
                    'terlambat_count'  => 0,
                    'terlambat_menit'  => 0,
                    'lembur_menit'     => 0,
                ];
            }
            $item->rekap = $data;
            return $item;
        });

        // Ambil daftar departemen unik untuk filter dropdown
        $departments = User::whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        // Semua karyawan untuk manual-override dropdown di Tab 2
        $allEmployees = User::with('profile')->get()->map(fn($u) => [
            'id'   => $u->id,
            'name' => $u->name,
            'dept' => $u->department ?? ($u->profile?->jabatan ?? ''),
            'init' => strtoupper(substr($u->name, 0, 1)),
        ])->toArray();

        return view('hr.absensi.index', compact('rekapList', 'bulan', 'departments', 'allEmployees'));
    }

    // ══════════════════════════════════════════════════════
    // TAB 2 — IMPORT FINGERPRINT + AI MATCHING
    // ══════════════════════════════════════════════════════

    /**
     * Step 1: Terima file Excel, parse, kirim ke Groq, kembalikan JSON.
     * TIDAK menyimpan apapun ke DB — hanya analisis.
     */
    public function processAI(Request $request)
    {
        if (!auth()->user()->hasRole('BPH')) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:5120',
        ], [
            'file.required' => 'File harus diunggah',
            'file.mimes'    => 'Format file harus .xls atau .xlsx',
            'file.max'      => 'Ukuran maksimal 5MB',
        ]);

        try {
            $file     = $request->file('file');
            $fullPath = $file->getRealPath();
            $ext      = strtolower($file->getClientOriginalExtension());

            // Parse Excel
            $excelData = $this->parseExcel($fullPath, $ext);

            // Ambil semua nama karyawan dari DB untuk dikirim ke Groq
            $employees = User::with('profile')
                ->get()
                ->map(fn($u) => [
                    'id'               => $u->id,
                    'name'             => $u->name,
                    'nama_fingerprint' => $u->profile?->nama_fingerprint ?? '',
                    'department'       => $u->department ?? ($u->profile?->jabatan ?? ''),
                ]);

            $apiKey = env('GROQ_API_KEY');
            if (!$apiKey) {
                throw new \Exception('API Key Groq tidak ditemukan di .env');
            }

            // Bangun string list karyawan untuk prompt
            $employeeList = $employees->map(fn($e) =>
                "ID:{$e['id']} | {$e['name']}" .
                ($e['nama_fingerprint'] ? " (alias: {$e['nama_fingerprint']})" : '') .
                ($e['department'] ? " | Dept: {$e['department']}" : '')
            )->implode("\n");

            $rows = [];

            foreach ($excelData['rows'] as $row) {
                $namaFinger = $row['nama'];

                // Cek dulu exact match via nama_fingerprint (fast path)
                $exactUser = $employees->first(fn($e) =>
                    strtolower(trim($e['nama_fingerprint'])) === strtolower(trim($namaFinger)) ||
                    strtolower(trim($e['name'])) === strtolower(trim($namaFinger))
                );

                if ($exactUser) {
                    $rows[] = array_merge($row, [
                        'matched_name' => $exactUser['name'],
                        'user_id'      => $exactUser['id'],
                        'confidence'   => 100,
                        'alternatives' => [],
                    ]);
                    continue;
                }

                // Kirim ke Groq untuk fuzzy matching
                $prompt = "Dari daftar nama karyawan berikut:\n{$employeeList}\n\n" .
                    "Nama mana yang paling cocok dengan: '{$namaFinger}'?\n" .
                    "Berikan jawaban dalam format JSON murni (tanpa markdown, tanpa backtick):\n" .
                    '{"match":"nama yang cocok","user_id":id_karyawan,"confidence":angka_0_100,' .
                    '"alternatives":[{"name":"nama lain","user_id":id,"confidence":angka},' .
                    '{"name":"nama lain","user_id":id,"confidence":angka}]}' . "\n" .
                    "Jika tidak ada yang cocok, confidence harus di bawah 40.";

                $response = Http::timeout(60)
                    ->withoutVerifying()
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model'       => 'llama-3.3-70b-versatile',
                        'messages'    => [
                            [
                                'role'    => 'system',
                                'content' => 'Kamu adalah asisten HR. Kembalikan HANYA JSON murni tanpa markdown, tanpa backtick, tanpa penjelasan apapun.',
                            ],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0,
                    ]);

                if (!$response->successful()) {
                    // Fallback: tandai sebagai tidak dikenal
                    $rows[] = array_merge($row, [
                        'matched_name' => null,
                        'user_id'      => null,
                        'confidence'   => 0,
                        'alternatives' => [],
                    ]);
                    continue;
                }

                $rawText = $response->json()['choices'][0]['message']['content'] ?? '';
                // Bersihkan markdown jika ada
                $rawText = preg_replace('/^```json\s*/i', '', trim($rawText));
                $rawText = preg_replace('/^```\s*/i', '', $rawText);
                $rawText = preg_replace('/```$/', '', trim($rawText));

                $aiResult = json_decode($rawText, true);

                if (!$aiResult || !isset($aiResult['confidence'])) {
                    $rows[] = array_merge($row, [
                        'matched_name' => null,
                        'user_id'      => null,
                        'confidence'   => 0,
                        'alternatives' => [],
                    ]);
                    continue;
                }

                $rows[] = array_merge($row, [
                    'matched_name' => $aiResult['match'] ?? null,
                    'user_id'      => $aiResult['user_id'] ?? null,
                    'confidence'   => (int) ($aiResult['confidence'] ?? 0),
                    'alternatives' => $aiResult['alternatives'] ?? [],
                ]);
            }

            return response()->json([
                'success' => true,
                'periode' => $excelData['periode'],
                'rows'    => $rows,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Step 3: Terima baris yang sudah dikonfirmasi HR, simpan ke DB.
     * Hanya baris dengan skip !== true dan user_id valid yang disimpan.
     */
    public function confirmImport(Request $request)
    {
        if (!auth()->user()->hasRole('BPH')) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'rows'   => 'required|array',
            'periode' => 'nullable|string',
        ]);

        $imported = 0;
        $skipped  = 0;
        $periodeStr = $request->periode ?? Carbon::now()->format('F Y');

        foreach ($request->rows as $row) {
            // Skip baris yang ditandai HR sebagai lewati
            if (!empty($row['skip'])) {
                $skipped++;
                continue;
            }

            $userId = $row['user_id'] ?? null;
            if (!$userId) {
                $skipped++;
                continue;
            }

            // Pastikan user ada
            $user = User::find($userId);
            if (!$user) {
                $skipped++;
                continue;
            }

            $hariHadir     = (int) ($row['hari_hadir'] ?? 0);
            $hariDibutuhkan = (int) ($row['hari_dibutuhkan'] ?? 0);
            $status = $hariHadir > 0 ? 'hadir' : 'alpha';

            // Tanggal: ambil dari row atau default ke tgl 1 bulan ini
            $tanggal = Carbon::now()->format('Y-m') . '-01';

            $keterangan = json_encode([
                'periode'          => $periodeStr,
                'hari_dibutuhkan'  => $hariDibutuhkan,
                'hari_hadir'       => $hariHadir,
                'hari_tidak_hadir' => (int) ($row['hari_tidak_hadir'] ?? 0),
                'terlambat_count'  => (int) ($row['terlambat_count'] ?? 0),
                'terlambat_menit'  => (int) ($row['terlambat_menit'] ?? 0),
                'lembur_menit'     => (int) ($row['lembur_menit'] ?? 0),
            ]);

            Absensi::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $tanggal],
                [
                    'status'     => $status,
                    'keterangan' => $keterangan,
                    'jam_masuk'  => null,
                    'jam_keluar' => null,
                ]
            );

            $imported++;
        }

        return response()->json([
            'success'  => true,
            'imported' => $imported,
            'skipped'  => $skipped,
        ]);
    }

    /**
     * Map nama fingerprint ke user (dipanggil via AJAX dari Tab 2).
     */
    public function mapFingerprint(Request $request)
    {
        if (!auth()->user()->hasRole('BPH')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'nama_fingerprint' => 'required|string',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            if ($user->profile) {
                $user->profile->update(['nama_fingerprint' => $request->nama_fingerprint]);
            } else {
                $user->profile()->create(['nama_fingerprint' => $request->nama_fingerprint]);
            }
            return response()->json(['success' => true, 'message' => 'Mapping berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    // TAB 3 — REKAP & LAPORAN (JSON untuk Chart.js)
    // ══════════════════════════════════════════════════════

    /**
     * Kembalikan data rekap untuk Tab 3 charts + table via AJAX.
     */
    public function rekapData(Request $request)
    {
        if (!auth()->user()->hasRole('BPH')) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $bulan = $request->bulan ?? date('Y-m');
        $tanggalMulai = $bulan . '-01';

        $query = Absensi::with(['user.profile', 'user' => fn($q) => $q->select('id', 'name', 'department')])
            ->where('tanggal', $tanggalMulai);

        if ($request->departemen) {
            $query->whereHas('user', fn($q) => $q->where('department', $request->departemen));
        }

        $list = $query->get();

        // Hitung distribusi status dari data rekap JSON
        $totalHadir     = 0;
        $totalIzin      = 0;
        $totalAlpha     = 0;
        $totalTerlambat = 0;

        $employeeSummary = [];

        foreach ($list as $item) {
            $data = json_decode($item->keterangan, true) ?? [];

            $hariHadir    = (int) ($data['hari_hadir'] ?? 0);
            $hariDibut    = (int) ($data['hari_dibutuhkan'] ?? 0);
            $hariAlpha    = (int) ($data['hari_tidak_hadir'] ?? 0);
            $telatCount   = (int) ($data['terlambat_count'] ?? 0);
            $lemburMenit  = (int) ($data['lembur_menit'] ?? 0);

            $totalHadir     += $hariHadir;
            $totalAlpha     += $hariAlpha;
            $totalTerlambat += $telatCount;

            $pct = $hariDibut > 0 ? round(($hariHadir / $hariDibut) * 100, 1) : 0;

            $employeeSummary[] = [
                'nama'        => $item->user?->name ?? '-',
                'departemen'  => $item->user?->department ?? ($item->user?->profile?->jabatan ?? '-'),
                'hadir'       => $hariHadir,
                'izin'        => 0, // izin tersimpan terpisah; default 0
                'alpha'       => $hariAlpha,
                'terlambat'   => $telatCount,
                'lembur_jam'  => round($lemburMenit / 60, 2),
                'pct'         => $pct,
                'dibutuhkan'  => $hariDibut,
            ];
        }

        // Hitung rata-rata kehadiran
        $avgPct = count($employeeSummary) > 0
            ? round(collect($employeeSummary)->avg('pct'), 1)
            : 0;

        // Daily chart: distribusi per hari bulan tersebut
        // Karena data model ini adalah rekap bulanan (bukan harian),
        // kita buat distribusi sederhana: hadir/alpha berdasarkan total per karyawan
        $daysInMonth = Carbon::parse($tanggalMulai)->daysInMonth;
        $daily = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $daily[$d] = ['hadir' => 0, 'alpha' => 0, 'izin' => 0];
        }

        // Distribusi rata-rata per hari dari data rekap
        foreach ($employeeSummary as $emp) {
            if ($emp['dibutuhkan'] > 0) {
                $perHariHadir = round($emp['hadir'] / $emp['dibutuhkan'] * $daysInMonth);
                for ($d = 1; $d <= min($perHariHadir, $daysInMonth); $d++) {
                    $daily[$d]['hadir']++;
                }
                for ($d = $perHariHadir + 1; $d <= $daysInMonth; $d++) {
                    $daily[$d]['alpha']++;
                }
            }
        }

        return response()->json([
            'success'      => true,
            'daily'        => array_values($daily),
            'distribution' => [
                'hadir'     => $totalHadir,
                'izin'      => $totalIzin,
                'alpha'     => $totalAlpha,
                'terlambat' => $totalTerlambat,
            ],
            'employees'    => $employeeSummary,
            'totals' => [
                'hadir'   => $totalHadir,
                'izin'    => $totalIzin,
                'alpha'   => $totalAlpha,
                'avg_pct' => $avgPct,
            ],
            'days_in_month' => $daysInMonth,
        ]);
    }

    // ══════════════════════════════════════════════════════
    // EXPORT
    // ══════════════════════════════════════════════════════

    public function exportExcel(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['hr', 'admin', 'super-admin'])) {
            abort(403, 'Akses ditolak. Hanya HR/Admin yang dapat mengekspor data absensi.');
        }

        $bulan = $request->bulan ?? date('Y-m');
        $tanggalMulai = $bulan . '-01';

        $absensiList = Absensi::with(['user.profile', 'user' => fn($q) => $q->select('id', 'name')])
            ->where('tanggal', $tanggalMulai)
            ->get();

        $namaFile = 'rekap-absensi-' . $bulan . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
        ];

        $callback = function () use ($absensiList) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['No', 'Nama Karyawan', 'Departemen/Jabatan', 'Hari Dibutuhkan', 'Hari Hadir', 'Tidak Hadir', 'Terlambat (Kali)', 'Terlambat (Menit)', 'Lembur (Menit)', 'Lembur (Jam)']);
            foreach ($absensiList as $i => $absensi) {
                $data = json_decode($absensi->keterangan, true) ?? [];
                $lemburJam = isset($data['lembur_menit']) ? round($data['lembur_menit'] / 60, 2) : 0;
                fputcsv($file, [
                    $i + 1,
                    $absensi->user?->name ?? '-',
                    $absensi->user?->profile?->jabatan ?? '-',
                    $data['hari_dibutuhkan'] ?? 0,
                    $data['hari_hadir'] ?? 0,
                    $data['hari_tidak_hadir'] ?? 0,
                    $data['terlambat_count'] ?? 0,
                    $data['terlambat_menit'] ?? 0,
                    $data['lembur_menit'] ?? 0,
                    $lemburJam,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['hr', 'admin', 'super-admin'])) {
            abort(403, 'Akses ditolak. Hanya HR/Admin yang dapat mengekspor data absensi.');
        }

        $bulan = $request->bulan ?? date('Y-m');
        $tanggalMulai = $bulan . '-01';

        $absensiList = Absensi::with(['user.profile', 'user' => fn($q) => $q->select('id', 'name')])
            ->where('tanggal', $tanggalMulai)
            ->get();

        $rekapList = $absensiList->map(function ($item) {
            $data = json_decode($item->keterangan, true);
            if (!is_array($data)) {
                $data = ['periode' => '-', 'hari_dibutuhkan' => 0, 'hari_hadir' => 0, 'hari_tidak_hadir' => 0, 'terlambat_count' => 0, 'terlambat_menit' => 0, 'lembur_menit' => 0];
            }
            $item->rekap = $data;
            return $item;
        });

        $ringkasan = [
            'total_karyawan'      => $rekapList->count(),
            'total_hadir'         => $rekapList->sum(fn($i) => $i->rekap['hari_hadir'] ?? 0),
            'total_alfa'          => $rekapList->sum(fn($i) => $i->rekap['hari_tidak_hadir'] ?? 0),
            'total_terlambat_kali'=> $rekapList->sum(fn($i) => $i->rekap['terlambat_count'] ?? 0),
            'total_lembur_jam'    => round($rekapList->sum(fn($i) => $i->rekap['lembur_menit'] ?? 0) / 60, 2),
        ];

        $pdf = Pdf::loadView('hr.absensi.export-pdf', compact('rekapList', 'bulan', 'ringkasan'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('rekap-absensi-' . $bulan . '.pdf');
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasAnyRole(['hr', 'admin', 'super-admin'])) {
            abort(403, 'Akses ditolak.');
        }

        try {
            Absensi::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════

    /**
     * Parse file Excel fingerprint jadi array rows.
     */
    private function parseExcel(string $fullPath, string $ext): array
    {
        $reader = $ext === 'xls'
            ? IOFactory::createReader('Xls')
            : IOFactory::createReader('Xlsx');

        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load($fullPath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false);

        $periodeStr = trim((string) ($rows[1][1] ?? '-'));

        $dataRows = [];

        foreach ($rows as $index => $row) {
            if ($index < 4) continue;

            $no   = trim((string) ($row[0] ?? ''));
            $nama = trim((string) ($row[1] ?? ''));

            if (empty($nama) || strtolower($nama) === 'nan' || strtolower($nama) === 'nama' || $this->isRomanNumeral($no)) {
                continue;
            }

            $kehadiran      = trim((string) ($row[11] ?? '0/0'));
            $parts          = explode('/', $kehadiran);
            $hariDibutuhkan = (int) ($parts[0] ?? 0);
            $hariHadir      = (int) ($parts[1] ?? 0);

            $terlambatCount   = (int) ($row[5] ?? 0);
            $terlambatMenit   = (int) ($row[6] ?? 0);
            $lemburReguler    = $this->convertLemburToMinutes($row[9] ?? '0');
            $lemburSpesial    = $this->convertLemburToMinutes($row[10] ?? '0');
            $lemburTotalMenit = $lemburReguler + $lemburSpesial;
            $tidakHadir       = (int) ($row[13] ?? 0);

            $dataRows[] = [
                'nama'            => $nama,
                'hari_dibutuhkan' => $hariDibutuhkan,
                'hari_hadir'      => $hariHadir,
                'hari_tidak_hadir'=> $tidakHadir,
                'terlambat_count' => $terlambatCount,
                'terlambat_menit' => $terlambatMenit,
                'lembur_menit'    => $lemburTotalMenit,
            ];
        }

        return ['periode' => $periodeStr, 'rows' => $dataRows];
    }

    private function convertLemburToMinutes($val): int
    {
        $str = trim((string) $val);
        if (empty($str) || strtolower($str) === 'nan' || strtolower($str) === 'null') return 0;
        $str    = str_replace(',', '.', $str);
        $parts  = explode('.', $str);
        $hours  = (int) ($parts[0] ?? 0);
        $minutes= (int) ($parts[1] ?? 0);
        return ($hours * 60) + $minutes;
    }

    private function isRomanNumeral($str): bool
    {
        $pattern = '/^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/';
        return preg_match($pattern, strtoupper(trim($str))) === 1;
    }
}