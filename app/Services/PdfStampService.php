<?php

namespace App\Services;

use App\Models\Surat;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class PdfStampService
{
    public function stamp(Surat $surat): string
    {
        // 1. Get coordinates from $surat->ttd_coordinates
        $coords = $surat->ttd_coordinates;
        if (!$coords) {
            throw new \Exception("Coordinates not found for stamp mode.");
        }

        // 2. Get approval steps
        $approvals = $surat->approvals()
            ->where('status', 'approved')
            ->with('approver.profile')
            ->get();

        // 3. Load original PDF using FPDI
        $originalPdfPath = storage_path('app/public/' . $surat->file_pdf);
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($originalPdfPath);

        // 4. For each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Find any coordinates for this page
            foreach ($approvals as $approval) {
                $jabatan = $approval->jabatan;
                if (isset($coords[$jabatan]) && $coords[$jabatan]['page'] == $pageNo) {
                    $coord = $coords[$jabatan];
                    
                    // Convert percentage to pixels/points
                    $x = ($coord['x'] / 100) * $size['width'];
                    $y = ($coord['y'] / 100) * $size['height'];

                    $signature = null;
                    if ($approval->approver && $approval->approver->profile) {
                        $signature = $approval->approver->profile->signature_path ?? $approval->approver->profile->ttd_path;
                    }
                    
                    if ($signature) {
                        // TTD image path
                        $ttdPath = public_path('storage/' . $signature);
                        
                        if (file_exists($ttdPath)) {
                            // Scale TTD image to roughly 40x20mm (adjusted from 60x25mm for better fit)
                            // We subtract half of width/height to center the stamp on the click point
                            $w = 40; 
                            $h = 20;
                            $pdf->Image($ttdPath, $x - ($w/2), $y - ($h/2), $w, $h);
                        }
                    }
                }
            }
        }

        // 5. Save to storage/app/public/final-pdf/{surat_id}_final.pdf
        $finalFilename = $surat->id . '_final.pdf';
        $finalDir = storage_path('app/public/final-pdf');
        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0755, true);
        }
        $finalPath = $finalDir . '/' . $finalFilename;
        $pdf->Output('F', $finalPath);

        // 6. Return the relative path
        return 'final-pdf/' . $finalFilename;
    }
}
