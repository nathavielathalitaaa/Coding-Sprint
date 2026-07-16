<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\User;
use App\Models\OrganisasiMember;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiAkhirController extends Controller
{
    public function __construct(
        private NotificationService $notifService
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        $query = Surat::where('status', 'approved_owner')
            ->whereHas('suratType', function ($q) {
                $q->where('requires_kegiatan_detail', true);
            });

        // Filter: Admin/Super-admin sees all, Pembina only sees their organization
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $orgIds = OrganisasiMember::where('user_id', $user->id)
                ->where('jabatan', 'pembina')
                ->pluck('organisasi_id');

            $query->whereIn('organisasi_id', $orgIds);
        }

        $surats = $query->with(['kegiatanDetail', 'organisasi.members.user', 'picUser'])
            ->latest()
            ->get();

        return view('pelaksanaan.disposisi-akhir', compact('surats'));
    }

    public function assign(Request $request, Surat $surat)
    {
        $user = Auth::user();

        $isAuthorized = $user->hasRole('admin') || $user->hasRole('super-admin') ||
            ($surat->organisasi && OrganisasiMember::where('organisasi_id', $surat->organisasi_id)
                ->where('user_id', $user->id)
                ->where('jabatan', 'pembina')
                ->exists());

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki hak untuk melakukan disposisi pada surat ini.');
        }

        $request->validate([
            'pic_user_id' => 'required|exists:users,id'
        ]);

        $isMember = OrganisasiMember::where('organisasi_id', $surat->organisasi_id)
            ->where('user_id', $request->pic_user_id)
            ->exists();

        if (!$isMember) {
            flash()->error('User yang dipilih harus merupakan anggota organisasi yang sama.');
            return redirect()->back();
        }

        $surat->update([
            'pic_user_id' => $request->pic_user_id
        ]);

        $newPic = User::find($request->pic_user_id);
        $kegiatanNama = $surat->kegiatanDetail->nama_kegiatan ?? $surat->perihal;

        $this->notifService->send(
            $newPic->id,
            'Penugasan PIC Kegiatan',
            "Anda ditugaskan sebagai PIC untuk pelaksanaan kegiatan: {$kegiatanNama}.",
            route('pelaksanaan.index')
        );

        flash()->success("Berhasil menugaskan {$newPic->name} sebagai PIC kegiatan.");
        return redirect()->back();
    }
}
