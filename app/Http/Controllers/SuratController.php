<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Http\Requests\Surat\StoreSuratRequest;
use Illuminate\Http\Request;
use Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratController extends Controller
{
    /**
     * Middleware untuk controller
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman daftar surat
     */
    public function index()
    {
        $surats = Surat::with('user')->latest()->paginate(10);
        return view('surat.index', compact('surats'));
    }

    /**
     * Halaman form buat surat baru
     */
    public function create()
    {
        return view('surat.create');
    }

    /**
     * Simpan surat baru
     */
    public function store(StoreSuratRequest $request)
    {
        $this->authorize('store', Surat::class);

        $validated = $request->validated();

        $fileName = null;
        if ($request->hasFile('file_pdf')) {
            $fileName = $request->file('file_pdf')->store('surat', 'public');
        }

        Surat::create([
            'user_id' => Auth::id(),
            'nomor_surat' => $this->generateNomorSurat(),
            'jenis_surat' => $validated['jenis_surat'],
            'perihal' => $validated['perihal'],
            'file_pdf' => $fileName,
            'status' => 'submitted',
        ]);

        return redirect()->route('surat.index')
            ->with('success', 'Surat berhasil dibuat');
    }

    /**
     * Tampilkan detail surat
     */
    public function show(Surat $surat)
    {
        return view('surat.show', compact('surat'));
    }

    /**
     * Halaman edit surat (revisi)
     */
    public function edit(Surat $surat)
    {
        $this->authorize('edit', $surat);
        return view('surat.edit', compact('surat'));
    }

    /**
     * Update surat (revisi file PDF)
     */
    public function update(StoreSuratRequest $request, Surat $surat)
    {
        $this->authorize('update', $surat);

        $validated = $request->validated();

        if ($request->hasFile('file_pdf')) {
            // Hapus file lama jika ada
            if ($surat->file_pdf && file_exists(storage_path('app/public/' . $surat->file_pdf))) {
                unlink(storage_path('app/public/' . $surat->file_pdf));
            }

            // Simpan file baru
            $fileName = $request->file('file_pdf')->store('surat', 'public');
            $surat->update(['file_pdf' => $fileName]);
        }

        // Update status menjadi submitted kembali
        $surat->update([
            'status' => 'submitted',
            'catatan_revisi' => null,
            'approved_by_supervisor' => null,
            'approved_by_owner' => null,
        ]);

        return redirect()->route('surat.show', $surat->id)
            ->with('success', 'Surat berhasil direvisi dan dikirim kembali untuk approval');
    }

    /**
     * Approval oleh supervisor
     */
    public function approveSupervisor(Request $request, Surat $surat)
    {
        $this->authorize('approveSupervisor', $surat);

        $surat->update([
            'status' => 'approved_supervisor',
            'approved_by_supervisor' => Auth::id(),
        ]);

        return redirect()->route('surat.show', $surat->id)
            ->with('success', 'Surat berhasil disetujui supervisor');
    }

    /**
     * Penolakan oleh supervisor
     */
    public function rejectSupervisor(Request $request, Surat $surat)
    {
        $this->authorize('rejectSupervisor', $surat);

        $request->validate([
            'catatan_revisi' => 'required|string|min:5',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi',
            'catatan_revisi.min' => 'Catatan revisi minimal 5 karakter',
        ]);

        $surat->update([
            'status' => 'revised',
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        return redirect()->route('surat.show', $surat->id)
            ->with('success', 'Surat ditolak. Pengguna telah diberitahu untuk merevisi');
    }

    /**
     * Approval oleh owner (admin)
     */
    public function approveOwner(Request $request, Surat $surat)
    {
        $this->authorize('approveOwner', $surat);

        $surat->update([
            'status' => 'approved_owner',
            'approved_by_owner' => Auth::id(),
        ]);

        return redirect()->route('surat.show', $surat->id)
            ->with('success', 'Surat berhasil disetujui oleh Owner');
    }

    /**
     * Penolakan oleh owner (admin)
     */
    public function rejectOwner(Request $request, Surat $surat)
    {
        $this->authorize('rejectOwner', $surat);

        $request->validate([
            'catatan_revisi' => 'required|string|min:5',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi',
            'catatan_revisi.min' => 'Catatan revisi minimal 5 karakter',
        ]);

        $surat->update([
            'status' => 'revised',
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        return redirect()->route('surat.show', $surat->id)
            ->with('success', 'Surat ditolak owner. Pengguna telah diberitahu untuk merevisi');
    }

    /**
     * Generate nomor surat otomatis
     */
    private function generateNomorSurat()
    {
        $count = Surat::whereYear('created_at', now()->year)->count() + 1;
        $bulan = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $tahun = now()->year;
        
        return sprintf('SURAT/%d/%s/%03d', $tahun, $bulan, $count);
    }

    /**
     * Download file PDF surat
     */
    public function download(Surat $surat)
    {
        $this->authorize('download', $surat);

        if (!$surat->file_pdf) {
            return redirect()->route('surat.show', $surat->id)
                ->with('error', 'File PDF tidak tersedia');
        }

        $filePath = storage_path('app/public/' . $surat->file_pdf);

        if (!file_exists($filePath)) {
            return redirect()->route('surat.show', $surat->id)
                ->with('error', 'File PDF tidak ditemukan');
        }

        return response()->download($filePath, $surat->nomor_surat . '.pdf');
    }
}
