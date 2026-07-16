<?php

namespace App\Http\Controllers;

use App\Models\LaporanPertanggungjawaban;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan Database Arsip LPJ dengan pencarian & filter.
     */
    public function index(Request $request)
    {
        $query = LaporanPertanggungjawaban::where('status', 'valid')
            ->with(['surat.kegiatanDetail', 'surat.organisasi', 'verifiedBy']);

        // Filter Organisasi
        if ($request->filled('organisasi_id')) {
            $query->whereHas('surat', function ($q) use ($request) {
                $q->where('organisasi_id', $request->organisasi_id);
            });
        }

        // Filter Tahun Pengarsipan
        if ($request->filled('tahun')) {
            $query->whereYear('archived_at', $request->tahun);
        }

        // 1. Filter Pencarian Keywords & Semantic Search
        $lpjs = collect();
        $isSemanticActive = false;
        if ($request->filled('q')) {
            // Base FULLTEXT search query
            $textQuery = clone $query;
            $textResults = $textQuery->search($request->q)->latest('archived_at')->get();

            // Attempt semantic search via EmbeddingService
            $embeddingService = new \App\Services\EmbeddingService();
            $queryVector = $embeddingService->embed($request->q);

            if ($queryVector !== null) {
                // Get all valid LPJs with embedding_vector not null (respecting filters)
                $candidatesQuery = clone $query;
                $candidates = $candidatesQuery->whereNotNull('embedding_vector')->get();

                $scored = [];
                foreach ($candidates as $lpj) {
                    $vector = $lpj->embedding_vector;
                    if (is_array($vector)) {
                        $lpj->similarity = \App\Support\CosineSimilarity::calculate($queryVector, $vector);
                        $scored[] = $lpj;
                    }
                }

                // Sort by highest similarity score
                usort($scored, function ($a, $b) {
                    return $b->similarity <=> $a->similarity;
                });

                // Merge: semantic sorted results first, then remaining FULLTEXT results
                $seenIds = [];
                $merged = collect();

                foreach ($scored as $lpj) {
                    $merged->push($lpj);
                    $seenIds[$lpj->id] = true;
                }

                foreach ($textResults as $lpj) {
                    if (!isset($seenIds[$lpj->id])) {
                        $lpj->similarity = 0.0;
                        $merged->push($lpj);
                        $seenIds[$lpj->id] = true;
                    }
                }

                $lpjs = $merged;
                $isSemanticActive = true;
            } else {
                $lpjs = $textResults;
            }
        } else {
            $lpjs = $query->latest('archived_at')->get();
        }

        // Ambil data pendukung filter
        $organizations = Organisasi::active()->orderBy('nama')->get();

        // Ambil tahun arsip unik secara dinamis
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            $years = LaporanPertanggungjawaban::where('status', 'valid')
                ->whereNotNull('archived_at')
                ->selectRaw('DISTINCT YEAR(archived_at) as year')
                ->pluck('year')
                ->toArray();
        } else {
            $years = LaporanPertanggungjawaban::where('status', 'valid')
                ->whereNotNull('archived_at')
                ->selectRaw('DISTINCT strftime("%Y", archived_at) as year')
                ->pluck('year')
                ->toArray();
        }

        $years = array_filter(array_map('intval', $years));
        rsort($years);

        return view('arsip.index', compact('lpjs', 'organizations', 'years', 'isSemanticActive'));
    }
}
