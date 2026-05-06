<?php

namespace App\Services;

use App\Models\ApprovalStep;
use App\Models\DocumentSetting;
use App\Models\Surat;
use App\Models\DocumentApproval;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * ApprovalCoverService
 * Mengelola pembuatan PDF cover approval.
 * Digunakan oleh: SuratController
 */
class ApprovalCoverService
{
    public function __construct(protected PdfMergeService $pdfMergeService)
    {
    }

    public function generateCover(Surat $surat): string
    {
        $documentType = 'surat_' . $surat->jenis_surat;

        // Ambil pengaturan dari step flow (override) atau global
        $step = ApprovalStep::where('document_type', $documentType)->first();
        $overrides = $step->setting_overrides ?? [];

        $settings = [
            'company_name' => $overrides['company_name'] ?? DocumentSetting::get('company_name', 'HR Sinergi Hotel & Villa'),
            'accent_color' => $overrides['accent_color'] ?? DocumentSetting::get('accent_color', '#04A54C'),
            'font_family'  => $overrides['font_family']  ?? DocumentSetting::get('font_family', 'Arial'),
            'footer_text'  => $overrides['footer_text']  ?? DocumentSetting::get('footer_text', 'Dokumen ini digenerate otomatis oleh sistem HR.'),
            'logo_path'    => $overrides['logo_path']    ?? DocumentSetting::get('logo_path', ''),
        ];

        // Logo base64
        $logoBase64 = null;
        if ($settings['logo_path']) {
            $fullLogoPath = storage_path('app/public/' . $settings['logo_path']);
            if (file_exists($fullLogoPath)) {
                $logoBase64 = 'data:image/' . pathinfo($fullLogoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($fullLogoPath));
            }
        }

        $steps = DocumentApproval::where('document_type', $documentType)
            ->where('document_id', $surat->id)
            ->orderBy('step_order')
            ->with('approver')
            ->get();

        $stepsWithTtd = $steps->map(function ($step) {
            $ttdBase64 = null;
            $approver = $step->approver;

            if ($approver && $approver->profile) {
                $signature = $approver->profile->signature_path ?? $approver->profile->ttd_path;
                
                if ($signature) {
                    $ttdPath = public_path('storage/' . $signature);
                    
                    if (file_exists($ttdPath)) {
                        $extension = pathinfo($ttdPath, PATHINFO_EXTENSION);
                        $mime = in_array(strtolower($extension), ['png', 'jpg', 'jpeg']) ? (strtolower($extension) === 'png' ? 'png' : 'jpeg') : 'png';
                        $ttdBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($ttdPath));
                    }
                }
            }
            return [
                'label'       => $step->label,
                'name'        => $step->approver?->name ?? '-',
                'actioned_at' => $step->actioned_at?->format('d M Y'),
                'catatan'     => $step->catatan,
                'ttd_base64'  => $ttdBase64,
                'status'      => $step->status,
            ];
        });

        $pdf = Pdf::loadView('surat.cover-approval', [
            'surat'    => $surat,
            'steps'    => $stepsWithTtd,
            'settings' => $settings,
            'logo_base64' => $logoBase64,
        ])->setPaper('A4', 'portrait');

        $filename = 'cover_approval_' . $surat->id . '_' . time() . '.pdf';
        $path = 'surat/covers/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function processMerge(Surat $surat): ?string
    {
        $documentType = 'surat_' . $surat->jenis_surat;
        $step = \App\Models\ApprovalStep::where('document_type', $documentType)->first();
        
        if ($step && $step->isModeAppend()) {
            $originalPdf = storage_path('app/public/' . $surat->file_pdf);
            $coverPdf = storage_path('app/public/' . $surat->cover_pdf_path);
            $outputPath = storage_path('app/public/final-pdf/' . $surat->id . '_final.pdf');
            
            $this->pdfMergeService->merge($originalPdf, $coverPdf, $outputPath);
            
            $finalPath = 'final-pdf/' . $surat->id . '_final.pdf';
            return $finalPath;
        }
        
        return null;
    }
}