<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SystemMonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|super-admin']);
    }

    public function index()
    {
        // 1. Log Statistics
        $logCount       = ActivityLog::count();
        $logsThisMonth  = ActivityLog::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->count();
        $logsToday      = ActivityLog::whereDate('created_at', today())->count();

        // Top 5 aksi terbanyak di log
        $topActions = ActivityLog::select('action', DB::raw('COUNT(*) as total'))
            ->groupBy('action')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 10 log aktivitas terbaru
        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // 2. Archive Statistics
        $archivePath   = storage_path('app/archives/logs');
        $archiveSize   = $this->getDirectorySize($archivePath);
        $totalArchives = Storage::disk('local')->exists('archives/logs')
                            ? count(Storage::disk('local')->files('archives/logs'))
                            : 0;

        // 3. Storage Statistics (Documents)
        $storagePath = storage_path('app/public/surat');
        $storageSize = $this->getDirectorySize($storagePath);
        $totalFiles  = File::exists($storagePath) ? count(File::files($storagePath)) : 0;

        // Hitung juga file surat turunan
        $storageTurunanPath  = storage_path('app/public/surat-turunan');
        $storageTurunanSize  = $this->getDirectorySize($storageTurunanPath);
        $totalTurunanFiles   = File::exists($storageTurunanPath) ? count(File::files($storageTurunanPath)) : 0;

        // 4. Database Statistics
        $dbSize = $this->getDatabaseSize();

        // 5. Surat Statistics
        $totalSurat         = Surat::count();
        $suratApproved      = Surat::where('status', 'approved_owner')->count();
        $suratPending       = Surat::whereIn('status', ['pending_admin', 'submitted'])->count();
        $suratBulanIni      = Surat::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count();

        // Surat per bulan (6 bulan terakhir) untuk grafik mini
        $suratPerBulan = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'bulan' => $date->translatedFormat('M Y'),
                'total' => Surat::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)->count(),
            ];
        });

        // 6. System Info
        $phpVersion     = PHP_VERSION;
        $laravelVersion = app()->version();
        $serverInfo     = request()->server('SERVER_SOFTWARE') ?? 'N/A';
        $memoryUsage    = $this->formatBytes(memory_get_usage(true));
        $uptime         = $this->getServerUptime();

        return view('system.monitor', compact(
            'logCount', 'logsThisMonth', 'logsToday',
            'topActions', 'recentLogs',
            'archiveSize', 'totalArchives',
            'storageSize', 'totalFiles',
            'storageTurunanSize', 'totalTurunanFiles',
            'dbSize',
            'totalSurat', 'suratApproved', 'suratPending', 'suratBulanIni',
            'suratPerBulan',
            'phpVersion', 'laravelVersion', 'serverInfo',
            'memoryUsage', 'uptime'
        ));
    }

    private function getDirectorySize($path)
    {
        if (!File::exists($path)) return 0;
        
        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        
        return $this->formatBytes($size);
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $query = "SELECT SUM(data_length + index_length) / 1024 / 1024 AS size 
                      FROM information_schema.TABLES 
                      WHERE table_schema = ?";
            
            $result = DB::select($query, [$dbName]);
            return number_format($result[0]->size, 2) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getServerUptime(): string
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                return 'N/A (Windows)';
            }
            $uptime = shell_exec('uptime -p 2>/dev/null');
            return $uptime ? trim(str_replace('up ', '', $uptime)) : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    public function archiveManager(Request $request)
    {
        // Ambil data surat yang punya file aktif (bukan ARCHIVED dan tidak null)
        $archivableSurats = Surat::where(function($q) {
                $q->where(function($q1) {
                    $q1->whereNotNull('file_pdf')->where('file_pdf', '!=', '')->where('file_pdf', '!=', 'ARCHIVED');
                })->orWhere(function($q2) {
                    $q2->whereNotNull('cover_pdf_path')->where('cover_pdf_path', '!=', '')->where('cover_pdf_path', '!=', 'ARCHIVED');
                })->orWhere(function($q3) {
                    $q3->whereNotNull('final_pdf_path')->where('final_pdf_path', '!=', '')->where('final_pdf_path', '!=', 'ARCHIVED');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Optional: filter by date in the controller if we want server-side filtering,
        // but since we only have a few records, we can pass all and let the view handle it 
        // or just pass them as a collection to be rendered.
        
        return view('system.archive-manager', compact('archivableSurats'));
    }

    public function archiveDocuments(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();

        // Cari surat yang punya file di antara tanggal tersebut
        $surats = Surat::whereBetween('created_at', [$request->start_date, $endDate])
            ->where(function($q) {
                $q->where(function($q1) {
                    $q1->whereNotNull('file_pdf')->where('file_pdf', '!=', '')->where('file_pdf', '!=', 'ARCHIVED');
                })->orWhere(function($q2) {
                    $q2->whereNotNull('cover_pdf_path')->where('cover_pdf_path', '!=', '')->where('cover_pdf_path', '!=', 'ARCHIVED');
                })->orWhere(function($q3) {
                    $q3->whereNotNull('final_pdf_path')->where('final_pdf_path', '!=', '')->where('final_pdf_path', '!=', 'ARCHIVED');
                });
            })
            ->get();

        if ($surats->isEmpty()) {
            return redirect()->back()->with('error', 'No documents found with attachments in the selected date range.');
        }

        $zipName = 'Archive_Surat_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '_' . time() . '.zip';
        $zipPath = storage_path('app/archives/' . $zipName);
        
        // Pastikan direktori ada
        if (!File::exists(storage_path('app/archives'))) {
            File::makeDirectory(storage_path('app/archives'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $filesAdded = 0;
            $filesToDelete = []; // Kumpulkan file yang akan dihapus SETELAH zip ditutup
            $suratsToUpdate = []; // Kumpulkan surat yang perlu diupdate di DB
            
            foreach ($surats as $surat) {
                $baseName = 'Surat_' . str_replace('/', '_', $surat->nomor_surat ?? $surat->id);
                $updateFields = [];
                
                // Cek dan tambahkan original file
                if ($surat->file_pdf && $surat->file_pdf !== 'ARCHIVED' && Storage::disk('public')->exists($surat->file_pdf)) {
                    $zip->addFile(Storage::disk('public')->path($surat->file_pdf), $baseName . '_Original.pdf');
                    $filesToDelete[] = $surat->file_pdf;
                    $updateFields['file_pdf'] = 'ARCHIVED';
                    $filesAdded++;
                }

                // Cek dan tambahkan cover file
                if ($surat->cover_pdf_path && $surat->cover_pdf_path !== 'ARCHIVED' && Storage::disk('public')->exists($surat->cover_pdf_path)) {
                    $zip->addFile(Storage::disk('public')->path($surat->cover_pdf_path), $baseName . '_Cover.pdf');
                    $filesToDelete[] = $surat->cover_pdf_path;
                    $updateFields['cover_pdf_path'] = 'ARCHIVED';
                    $filesAdded++;
                }

                // Cek dan tambahkan final file
                if ($surat->final_pdf_path && $surat->final_pdf_path !== 'ARCHIVED' && Storage::disk('public')->exists($surat->final_pdf_path)) {
                    $zip->addFile(Storage::disk('public')->path($surat->final_pdf_path), $baseName . '_Final.pdf');
                    $filesToDelete[] = $surat->final_pdf_path;
                    $updateFields['final_pdf_path'] = 'ARCHIVED';
                    $filesAdded++;
                }
                
                if (!empty($updateFields)) {
                    $suratsToUpdate[] = ['surat' => $surat, 'fields' => $updateFields];
                }
            }

            if ($filesAdded > 0) {
                // PENTING: Tutup zip DULU sebelum hapus file dan update DB
                // ZipArchive baru membaca file saat close() dipanggil
                $zip->close();

                // Setelah zip berhasil ditutup, baru hapus file asli dan update DB
                foreach ($filesToDelete as $fileToDelete) {
                    Storage::disk('public')->delete($fileToDelete);
                }

                foreach ($suratsToUpdate as $item) {
                    foreach ($item['fields'] as $field => $value) {
                        $item['surat']->{$field} = $value;
                    }
                    $item['surat']->save();
                }

                // Return ZIP file as a download and delete it after sending to save space
                return response()->download($zipPath)->deleteFileAfterSend(true);
            } else {
                $zip->close();
                // Hapus zip kosong
                File::delete($zipPath);
                return redirect()->back()->with('error', 'Files exist in database but are physically missing from server.');
            }
        } else {
            return redirect()->back()->with('error', 'Failed to create zip archive.');
        }
    }
}
