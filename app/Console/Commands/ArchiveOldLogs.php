<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use File;

class ArchiveOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'logs:archive {months=12 : Age of logs to archive in months}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Archive activity logs older than specified months into CSV files and remove them from database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->argument('months');
        $cutoffDate = Carbon::now()->subMonths($months);

        $this->info("Checking for logs older than {$cutoffDate->toDateTimeString()}...");

        $oldLogs = ActivityLog::where('created_at', '<', $cutoffDate)->orderBy('created_at', 'asc');
        $count = $oldLogs->count();

        if ($count === 0) {
            $this->info("No old logs found to archive.");
            return 0;
        }

        $filename = 'activity_logs_archive_' . Carbon::now()->format('Y_m_d_His') . '.csv';
        $directory = 'archives/logs';
        
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        $path = $directory . '/' . $filename;
        $fullPath = storage_path('app/' . $path);

        $this->info("Archiving {$count} logs to {$filename}...");

        // Open file for writing
        $file = fopen($fullPath, 'w');
        
        // Add CSV headers
        fputcsv($file, ['ID', 'User ID', 'Action', 'Target Type', 'Target ID', 'Description', 'IP Address', 'User Agent', 'Created At']);

        // Process in chunks to save memory
        $oldLogs->chunk(100, function ($logs) use ($file) {
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user_id,
                    $log->action,
                    $log->target_type,
                    $log->target_id,
                    $log->description,
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at,
                ]);
            }
        });

        fclose($file);

        // Delete archived logs from database
        ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Successfully archived and deleted {$count} logs.");
        
        // Log this action itself to the new log table
        ActivityLog::create([
            'action' => 'system_archive_logs',
            'description' => "System automatically archived {$count} logs into {$filename}",
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console/Scheduler'
        ]);

        return 0;
    }
}
