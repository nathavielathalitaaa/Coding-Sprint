<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

class PdfMergeService
{
    public function merge(string $originalPdfPath, string $coverPdfPath, string $outputPath): string
    {
        $pdf = new Fpdi();

        // 1. import all pages from originalpdfpath
        $pageCount1 = $pdf->setSourceFile($originalPdfPath);
        for ($pageNo = 1; $pageNo <= $pageCount1; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], $size);
            $pdf->useTemplate($templateId);
        }

        // 2. import all pages from coverpdfpath (the ttd page)
        $pageCount2 = $pdf->setSourceFile($coverPdfPath);
        for ($pageNo = 1; $pageNo <= $pageCount2; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], $size);
            $pdf->useTemplate($templateId);
        }

        // ensure the directory exists
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 3. save merged pdf to outputpath
        $pdf->Output('F', $outputPath);

        // 4. return outputpath
        return $outputPath;
    }
}
