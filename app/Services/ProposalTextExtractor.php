<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Illuminate\Support\Facades\Log;

class ProposalTextExtractor
{
    /**
     * Extract text content from a given file path.
     *
     * @param string $filePath
     * @param string $extension
     * @return string
     */
    public function extract(string $filePath, string $extension): string
    {
        if (!file_exists($filePath)) {
            Log::warning("Proposal file not found at: {$filePath}");
            return '';
        }

        $extension = strtolower($extension);

        try {
            if ($extension === 'pdf') {
                return $this->extractPdfText($filePath);
            } elseif ($extension === 'docx') {
                return $this->extractWordText($filePath);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to extract text from proposal ({$extension}): " . $e->getMessage(), [
                'exception' => $e
            ]);
            return '';
        }

        return '';
    }

    /**
     * Extract text from PDF using Smalot PDF Parser.
     */
    private function extractPdfText(string $filePath): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }

    /**
     * Extract text from Word (.docx) using PhpOffice PhpWord recursively.
     */
    private function extractWordText(string $filePath): string
    {
        $phpWord = WordIOFactory::load($filePath);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            $text .= $this->extractNodeText($section);
        }
        return $text;
    }

    /**
     * Recursively extract text from PhpWord nodes.
     */
    private function extractNodeText($node): string
    {
        $text = '';
        if (method_exists($node, 'getText')) {
            $nodeText = $node->getText();
            if (is_string($nodeText)) {
                $text .= $nodeText . "\n";
            }
        } elseif (method_exists($node, 'getElements')) {
            foreach ($node->getElements() as $element) {
                $text .= $this->extractNodeText($element);
            }
        } elseif (method_exists($node, 'getRows')) { // For tables
            foreach ($node->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    $text .= $this->extractNodeText($cell) . " ";
                }
                $text .= "\n";
            }
        }
        return $text;
    }
}
