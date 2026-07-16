<?php

namespace App\Http\Controllers;

use App\Models\Komisi;
use App\Models\KomisiMember;
use App\Models\Organisasi;
use App\Models\OrganisasiMember;
use App\Models\User;
use Illuminate\Http\Request;

class OrganisasiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|super-admin']);
    }

    /**
     * List semua organisasi (OSIS, MPK, Sub Organ).
     */
    public function index()
    {
        $osis      = Organisasi::where('tipe', 'osis')->with(['members.user'])->first();
        $mpk       = Organisasi::where('tipe', 'mpk')->with(['members.user', 'komisis.members.user'])->first();
        $subOrgans = Organisasi::where('tipe', 'sub_organ')->with(['members.user'])->get();

        return view('organisasi.index', compact('osis', 'mpk', 'subOrgans'));
    }

    /**
     * Form buat Sub Organ baru.
     * OSIS & MPK hanya dibuat sekali via seeder.
     */
    public function create()
    {
        return view('organisasi.create');
    }

    /**
     * Simpan Sub Organ baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        Organisasi::create([
            'nama'      => $request->nama,
            'tipe'      => 'sub_organ',
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        flash()->success("Sub Organ '{$request->nama}' berhasil dibuat.");
        return redirect()->route('organisasi.index');
    }

    /**
     * Detail organisasi: list anggota + jabatan.
     */
    public function show(Organisasi $organisasi)
    {
        $organisasi->load(['members.user', 'komisis.members.user']);

        // Ambil semua user yang belum jadi anggota organisasi ini
        $existingMemberIds = $organisasi->members->pluck('user_id')->toArray();
        $availableUsers = User::whereNotIn('id', $existingMemberIds)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $jabatanOptions = OrganisasiMember::jabatanOptions();

        return view('organisasi.show', compact('organisasi', 'availableUsers', 'jabatanOptions'));
    }

    /**
     * Assign user ke organisasi dengan jabatan tertentu.
     */
    public function addMember(Request $request, Organisasi $organisasi)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jabatan' => 'required|in:anggota,sekretaris,ketua,bph,komisi,pembina,pengawas',
        ]);

        // Cek duplikat
        $exists = OrganisasiMember::where('user_id', $request->user_id)
            ->where('organisasi_id', $organisasi->id)
            ->exists();

        if ($exists) {
            flash()->error('User sudah menjadi anggota organisasi ini.');
            return redirect()->back();
        }

        OrganisasiMember::create([
            'user_id'       => $request->user_id,
            'organisasi_id' => $organisasi->id,
            'jabatan'       => $request->jabatan,
        ]);

        $user = User::find($request->user_id);
        flash()->success("'{$user->name}' berhasil ditambahkan sebagai {$request->jabatan}.");
        return redirect()->back();
    }

    /**
     * Hapus user dari organisasi.
     */
    public function removeMember(Organisasi $organisasi, OrganisasiMember $member)
    {
        // Pastikan member memang dari organisasi ini
        if ($member->organisasi_id !== $organisasi->id) {
            abort(403);
        }

        $name = $member->user->name ?? 'User';
        $member->delete();

        flash()->success("'{$name}' berhasil dicopot dari organisasi.");
        return redirect()->back();
    }

    /**
     * Buat Komisi baru (khusus organisasi tipe MPK).
     */
    public function createKomisi(Request $request, Organisasi $organisasi)
    {
        if ($organisasi->tipe !== 'mpk') {
            abort(403, 'Komisi hanya bisa dibuat untuk organisasi MPK.');
        }

        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        Komisi::create([
            'nama'         => $request->nama,
            'organisasi_id'=> $organisasi->id,
            'deskripsi'    => $request->deskripsi,
            'is_active'    => true,
        ]);

        flash()->success("Komisi '{$request->nama}' berhasil dibuat.");
        return redirect()->back();
    }

    /**
     * Assign user ke Komisi MPK.
     */
    public function addKomisiMember(Request $request, Komisi $komisi)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Cek duplikat
        $exists = KomisiMember::where('user_id', $request->user_id)
            ->where('komisi_id', $komisi->id)
            ->exists();

        if ($exists) {
            flash()->error('User sudah menjadi anggota komisi ini.');
            return redirect()->back();
        }

        KomisiMember::create([
            'user_id'  => $request->user_id,
            'komisi_id'=> $komisi->id,
        ]);

        $user = User::find($request->user_id);
        flash()->success("'{$user->name}' berhasil ditambahkan ke komisi '{$komisi->nama}'.");
        return redirect()->back();
    }

    /**
     * Hapus user dari Komisi MPK.
     */
    public function removeKomisiMember(Komisi $komisi, KomisiMember $member)
    {
        if ($member->komisi_id !== $komisi->id) {
            abort(403);
        }

        $name = $member->user->name ?? 'User';
        $member->delete();

        flash()->success("'{$name}' berhasil dicopot dari komisi.");
        return redirect()->back();
    }
}
