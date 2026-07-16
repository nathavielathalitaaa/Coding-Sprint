<?php

namespace App\Services;

use App\Models\Surat;
use App\Models\ProposalFormatCheck;
use App\Models\ProposalReferenceText;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProposalFormatChecker
{
    const SECTIONS_WAJIB = [
        'latar_belakang'      => ['latar belakang'],
        'tujuan_kegiatan'     => ['tujuan kegiatan'],
        'manfaat_kegiatan'    => ['manfaat kegiatan'],
        'timeline_kegiatan'   => ['timeline kegiatan', 'jadwal kegiatan'],
        'susunan_kepanitiaan' => ['susunan kepanitiaan'],
        'penutup'             => ['penutup'],
    ];

    const SECTIONS_BONUS = [
        'sasaran_kegiatan' => ['sasaran kegiatan'],
        'konsep_kegiatan'  => ['konsep kegiatan'],
        'gambaran_kegiatan'=> ['gambaran kegiatan'],
        'saran'            => ['saran'],
    ];

    const RENCANA_ANGGARAN_KEYWORDS = ['rencana anggaran', 'perencanaan anggaran', 'rab'];
    const ANGGARAN_TIDAK_DIPERLUKAN_PHRASE = 'tidak memerlukan anggaran';

    public function __construct(
        protected ProposalTextExtractor $textExtractor,
        protected EmbeddingService $embeddingService
    ) {}

    /**
     * Check structure of the proposal text.
     *
     * @param string $text
     * @return array
     */
    public function checkStructure(string $text): array
    {
        // 1. Gather all keywords to search and their corresponding section key
        $allHeadingKeywords = [];
        foreach (self::SECTIONS_WAJIB as $key => $keywords) {
            foreach ($keywords as $kw) {
                $allHeadingKeywords[] = ['key' => $key, 'keyword' => $kw];
            }
        }
        foreach (self::SECTIONS_BONUS as $key => $keywords) {
            foreach ($keywords as $kw) {
                $allHeadingKeywords[] = ['key' => $key, 'keyword' => $kw];
            }
        }
        foreach (self::RENCANA_ANGGARAN_KEYWORDS as $kw) {
            $allHeadingKeywords[] = ['key' => 'rencana_anggaran', 'keyword' => $kw];
        }

        // Find positions of headings
        $matches = [];
        foreach ($allHeadingKeywords as $item) {
            $key = $item['key'];
            $keyword = $item['keyword'];
            // Case-insensitive search
            $pos = mb_stripos($text, $keyword);
            if ($pos !== false) {
                // If we already have a match for this key, keep the first one (lowest pos)
                if (!isset($matches[$key]) || $pos < $matches[$key]['pos']) {
                    $matches[$key] = [
                        'pos' => $pos,
                        'keyword_len' => mb_strlen($keyword),
                    ];
                }
            }
        }

        // Sort matches by start position ascending
        uasort($matches, function ($a, $b) {
            return $a['pos'] <=> $b['pos'];
        });

        // Determine section bounds and slice content
        $keys = array_keys($matches);
        $sectionsData = [];
        $textLen = mb_strlen($text);

        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            $currentPos = $matches[$key]['pos'];
            $currentKwLen = $matches[$key]['keyword_len'];

            $start = $currentPos + $currentKwLen;
            
            // End position is the start position of the next heading, or end of text
            $end = ($i + 1 < count($keys)) ? $matches[$keys[$i+1]]['pos'] : $textLen;
            
            $isiTeks = mb_substr($text, $start, $end - $start);
            $sectionsData[$key] = [
                'found' => true,
                'isi_teks' => trim($isiTeks),
            ];
        }

        // 2. Perform validations / checks for missing or special sections
        $result = [];

        // All wajib sections except rencana_anggaran
        foreach (self::SECTIONS_WAJIB as $key => $kws) {
            if (isset($sectionsData[$key])) {
                $result[$key] = $sectionsData[$key];
                $result[$key]['is_wajib'] = true;
            } else {
                $result[$key] = [
                    'found' => false,
                    'isi_teks' => '',
                    'is_wajib' => true,
                ];
            }
        }

        // Bonus sections
        foreach (self::SECTIONS_BONUS as $key => $kws) {
            if (isset($sectionsData[$key])) {
                $result[$key] = $sectionsData[$key];
                $result[$key]['is_wajib'] = false;
            } else {
                $result[$key] = [
                    'found' => false,
                    'isi_teks' => '',
                    'is_wajib' => false,
                ];
            }
        }

        // Handle rencana_anggaran
        if (isset($sectionsData['rencana_anggaran'])) {
            $result['rencana_anggaran'] = $sectionsData['rencana_anggaran'];
            $result['rencana_anggaran']['tidak_diperlukan'] = false;
            $result['rencana_anggaran']['is_wajib'] = true;
        } else {
            // Check for phrase
            $noAnggaranPhrases = [
                'tidak memerlukan anggaran',
                'tidak membutuhkan anggaran',
                'tidak perlu anggaran',
                'tidak membutuhkan biaya',
                'tidak memerlukan biaya',
            ];
            $tidakDiperlukan = false;
            foreach ($noAnggaranPhrases as $phrase) {
                if (mb_stripos($text, $phrase) !== false) {
                    $tidakDiperlukan = true;
                    break;
                }
            }

            $result['rencana_anggaran'] = [
                'found' => $tidakDiperlukan,
                'isi_teks' => '',
                'tidak_diperlukan' => $tidakDiperlukan,
                'is_wajib' => true,
            ];
        }

        // 3. Section validations for latar_belakang and penutup (paragraph & sentence count)
        foreach (['latar_belakang', 'penutup'] as $key) {
            if ($result[$key]['found']) {
                $isi = $result[$key]['isi_teks'];
                // split by blank lines
                $paragraphs = preg_split('/\n\s*\n/', $isi);
                $paragraphs = array_filter(array_map('trim', $paragraphs), function ($p) {
                    return $p !== '';
                });

                $totalSentences = 0;
                foreach ($paragraphs as $para) {
                    $sentences = preg_split('/[.!?]+/', $para);
                    $sentences = array_filter(array_map('trim', $sentences), function ($s) {
                        return $s !== '';
                    });
                    $totalSentences += count($sentences);
                }

                $paraCount = count($paragraphs);
                $avgKalimat = $paraCount > 0 ? $totalSentences / $paraCount : 0.0;

                $tooShort = ($paraCount < 3) || ($avgKalimat < 4.0);

                $result[$key]['terlalu_singkat'] = $tooShort;
                $result[$key]['paragraf_count'] = $paraCount;
                $result[$key]['avg_kalimat'] = $avgKalimat;
            } else {
                $result[$key]['terlalu_singkat'] = false;
                $result[$key]['paragraf_count'] = 0;
                $result[$key]['avg_kalimat'] = 0.0;
            }
        }

        return $result;
    }

    /**
     * Compare matched sections against reference texts using embedding cosine similarity.
     *
     * @param array $sectionsFound
     * @return array
     */
    public function checkContentQuality(array $sectionsFound): array
    {
        $scores = [];
        
        // Get all reference texts with embeddings once
        $references = ProposalReferenceText::whereNotNull('embedding_vector')->get();

        foreach ($sectionsFound as $key => &$section) {
            // Check if section is wajib
            $isWajib = $section['is_wajib'] ?? false;
            if (!$isWajib) {
                continue;
            }
            
            // Skip rencana_anggaran if tidak_diperlukan is true
            if ($key === 'rencana_anggaran' && ($section['tidak_diperlukan'] ?? false)) {
                continue;
            }

            // Only check if found and has isi_teks
            if ($section['found'] && !empty($section['isi_teks'])) {
                // Generate embedding via EmbeddingService
                $vector = $this->embeddingService->embed($section['isi_teks']);
                
                if ($vector !== null) {
                    $sectionRefs = $references->where('section_key', $key);
                    
                    if ($sectionRefs->isNotEmpty()) {
                        $maxSim = 0.0;
                        foreach ($sectionRefs as $ref) {
                            $refVector = $ref->embedding_vector;
                            if (is_array($refVector)) {
                                $sim = \App\Support\CosineSimilarity::calculate($vector, $refVector);
                                if ($sim > $maxSim) {
                                    $maxSim = $sim;
                                }
                            }
                        }
                        
                        $score = (int) round(max(0.0, $maxSim) * 100);
                        $section['skor_konten'] = $score;
                        $scores[] = $score;
                    } else {
                        $section['skor_konten'] = null;
                    }
                } else {
                    // Embedding service down, skip scoring this section from average, do not fail
                    $section['skor_konten'] = null;
                }
            }
        }
        unset($section);

        $skorKonten = count($scores) > 0 ? (int) round(array_sum($scores) / count($scores)) : null;

        return [
            'sections' => $sectionsFound,
            'skor_konten' => $skorKonten,
        ];
    }

    /**
     * Analyze proposal document and store/update format check details.
     *
     * @param Surat $surat
     * @return array
     */
    public function analyze(Surat $surat): array
    {
        if (!$surat->file_pdf) {
            return [
                'skor_struktur' => 0,
                'skor_konten' => null,
                'skor_akhir' => 0,
                'detail' => [],
            ];
        }

        $filePath = storage_path('app/public/' . $surat->file_pdf);
        $extension = strtolower(pathinfo($surat->file_pdf, PATHINFO_EXTENSION));

        // 1. Text Extraction
        $text = $this->textExtractor->extract($filePath, $extension);

        if (empty($text)) {
            $result = ProposalFormatCheck::updateOrCreate(
                ['surat_id' => $surat->id],
                [
                    'skor_struktur' => 0,
                    'skor_konten' => null,
                    'skor_akhir' => 0,
                    'detail' => ['error' => 'Failed to extract text or file is empty'],
                    'checked_at' => Carbon::now(),
                ]
            );
            return $result->toArray();
        }

        // 2. Structural checks
        $sections = $this->checkStructure($text);

        // Count scores for structural
        $wajibTerpenuhi = 0;
        foreach (self::SECTIONS_WAJIB as $key => $kws) {
            if ($sections[$key]['found']) {
                $wajibTerpenuhi++;
            }
        }
        if ($sections['rencana_anggaran']['found']) {
            $wajibTerpenuhi++;
        }

        $bonusDitemukan = 0;
        foreach (self::SECTIONS_BONUS as $key => $kws) {
            if ($sections[$key]['found']) {
                $bonusDitemukan++;
            }
        }

        $skorStruktur = (int) round(($wajibTerpenuhi / 7) * 80 + ($bonusDitemukan / 4) * 20);

        // 3. Content quality checks
        $contentQualityResult = $this->checkContentQuality($sections);
        $sectionsWithQuality = $contentQualityResult['sections'];
        $skorKonten = $contentQualityResult['skor_konten'];

        // 4. Combined final score
        if (is_null($skorKonten)) {
            $skorAkhir = $skorStruktur;
        } else {
            $skorAkhir = (int) round(($skorStruktur * 0.7) + ($skorKonten * 0.3));
        }

        // 5. Save/Update in DB
        $check = ProposalFormatCheck::updateOrCreate(
            ['surat_id' => $surat->id],
            [
                'skor_struktur' => $skorStruktur,
                'skor_konten' => $skorKonten,
                'skor_akhir' => $skorAkhir,
                'detail' => [
                    'sections' => $sectionsWithQuality,
                ],
                'checked_at' => Carbon::now(),
            ]
        );

        return $check->toArray();
    }
}
