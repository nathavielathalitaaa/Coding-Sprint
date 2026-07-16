<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProposalReferenceText;
use App\Services\ProposalTextExtractor;
use App\Services\ProposalFormatChecker;

class SeedReferences extends Command
{
    protected $signature = 'proposal:seed-references';
    protected $description = 'Seed standard proposal template and examples into reference database';

    public function handle(ProposalTextExtractor $extractor, ProposalFormatChecker $checker)
    {
        $dir = storage_path('app/proposal-references');
        if (!is_dir($dir)) {
            $this->error("Directory storage/app/proposal-references/ does not exist.");
            return 1;
        }

        // Find files
        $docxFiles = glob("{$dir}/*.docx");
        $pdfFiles = glob("{$dir}/*.pdf");

        $allFiles = array_merge($docxFiles, $pdfFiles);

        if (empty($allFiles)) {
            $this->error("No reference files (.docx or .pdf) found in {$dir}.");
            return 1;
        }

        $this->info("Truncating proposal_reference_texts table...");
        ProposalReferenceText::truncate();

        foreach ($allFiles as $file) {
            $filename = basename($file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $this->info("Processing reference file: {$filename}...");

            $text = $extractor->extract($file, $ext);
            if (empty($text)) {
                $this->error("Failed to extract text from {$filename} or file is empty.");
                continue;
            }

            $sections = $checker->checkStructure($text);
            $count = 0;

            foreach ($sections as $key => $section) {
                // If it is found and has content, save it as a reference text
                if ($section['found'] && !empty($section['isi_teks'])) {
                    ProposalReferenceText::create([
                        'section_key' => $key,
                        'contoh_teks' => $section['isi_teks'],
                        'embedding_vector' => null, // Will be filled by embed-references
                    ]);
                    $count++;
                }
            }

            $this->info("Successfully seeded {$count} sections from {$filename}.");
        }

        $this->info("Reference seeding completed successfully.");
        return 0;
    }
}
