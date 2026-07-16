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
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user->hasAnyRole(['admin', 'super-admin'])) {
                return $next($request);
            }

            $organisasi = $request->route('organisasi');
            $organisasiId = null;

            if ($organisasi instanceof \App\Models\Organisasi) {
                $organisasiId = $organisasi->id;
            } elseif (is_numeric($organisasi)) {
                $organisasiId = (int)$organisasi;
            }

            if ($organisasiId) {
                // Harus merupakan BPH atau Ketua dari organisasi spesifik ini
                $isBphOrKetuaOfThisOrg = OrganisasiMember::where('user_id', $user->id)
                    ->where('organisasi_id', $organisasiId)
                    ->whereIn('jabatan', ['bph', 'ketua'])
                    ->exists();

                if ($isBphOrKetuaOfThisOrg) {
                    return $next($request);
                }
            } else {
                // Untuk index/create/store, harus merupakan BPH atau Ketua dari minimal satu organisasi
                $isBphOrKetua = OrganisasiMember::where('user_id', $user->id)
                    ->whereIn('jabatan', ['bph', 'ketua'])
                    ->exists();

                if ($isBphOrKetua) {
                    return $next($request);
                }
            }

            abort(403, 'Anda tidak memiliki hak akses untuk mengelola organisasi ini.');
        });
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Dapatkan daftar organisasi yang boleh dikelola oleh user ini
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $organisasis = Organisasi::where('is_active', true)->orderBy('tipe')->get();
        } else {
            $myOrgIds = $user->organisasiMembers()
                ->whereIn('jabatan', ['bph', 'ketua'])
                ->pluck('organisasi_id')
                ->toArray();
            $organisasis = Organisasi::whereIn('id', $myOrgIds)->where('is_active', true)->get();
        }

        if ($organisasis->isEmpty()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola organisasi apa pun.');
        }

        // 2. Tentukan organisasi yang aktif (bisa dari input 'org_id' atau default yang pertama)
        $activeOrgId = $request->input('org_id', $organisasis->first()->id);
        $organisasi = $organisasis->firstWhere('id', $activeOrgId) ?: $organisasis->first();

        // 3. Muat relasi anggota & available users untuk organisasi aktif
        $organisasi->load(['members.user', 'komisis.members.user']);
        $existingMemberIds = $organisasi->members->pluck('user_id')->toArray();
        $availableUsers = User::whereNotIn('id', $existingMemberIds)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $jabatanOptions = OrganisasiMember::jabatanOptions();

        return view('organisasi.index', compact('organisasis', 'organisasi', 'availableUsers', 'jabatanOptions'));
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
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'role_name' => 'required|in:admin,guru,anggota',
            'jabatan'   => 'required|in:anggota,sekretaris,ketua,bph,pembina,pengawas',
            'komisi_id' => 'nullable|exists:komisis,id',
        ]);

        // Cek apakah user sudah ada
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $user = User::create([
                'name'                 => $request->name,
                'email'                => $request->email,
                'role_name'            => $request->role_name,
                'status'               => 'aktif',
                'password'             => 'password',
                'must_change_password' => true,
            ]);

            $roleExists = \Illuminate\Support\Facades\DB::table('roles')->where('name', $request->role_name)->exists();
            if ($roleExists) {
                $user->assignRole($request->role_name);
            } else {
                $user->assignRole('anggota');
            }
        }

        // Cek duplikat keanggotaan
        $exists = OrganisasiMember::where('user_id', $user->id)
            ->where('organisasi_id', $organisasi->id)
            ->exists();

        if ($exists) {
            flash()->error('Pengguna dengan email tersebut sudah terdaftar di organisasi ini.');
            return redirect()->back();
        }

        OrganisasiMember::create([
            'user_id'       => $user->id,
            'organisasi_id' => $organisasi->id,
            'jabatan'       => $request->jabatan,
        ]);

        if ($request->filled('komisi_id')) {
            $isKomisiMember = KomisiMember::where('user_id', $user->id)
                ->where('komisi_id', $request->komisi_id)
                ->exists();
            if (!$isKomisiMember) {
                KomisiMember::create([
                    'user_id'   => $user->id,
                    'komisi_id' => $request->komisi_id,
                ]);
            }
        }

        $term = $organisasi->tipe === 'osis' ? 'divisi' : 'komisi';
        $msg = "'{$user->name}' berhasil dibuat akunnya dan terdaftar sebagai {$request->jabatan}.";
        if ($request->filled('komisi_id')) {
            $komisi = Komisi::find($request->komisi_id);
            $msg .= " Serta dipetakan ke {$term} '{$komisi->nama}'.";
        }
        flash()->success($msg);
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
     * Buat Komisi baru (khusus organisasi tipe MPK atau OSIS).
     */
    public function createKomisi(Request $request, Organisasi $organisasi)
    {
        if ($organisasi->tipe !== 'mpk' && $organisasi->tipe !== 'osis') {
            abort(403, 'Komisi/Divisi hanya bisa dibuat untuk organisasi MPK atau OSIS.');
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

        $term = $organisasi->tipe === 'osis' ? 'Divisi' : 'Komisi';
        flash()->success("{$term} '{$request->nama}' berhasil dibuat.");
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

    /**
     * Download Excel template for bulk user registration.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Notes in rows 1 to 3
        $sheet->setCellValue('A1', 'CATATAN PENTING:');
        $sheet->setCellValue('B1', 'Password default akun baru adalah \'password\' (wajib diubah setelah login pertama). Jangan ubah header pada Row 4.');
        
        $sheet->setCellValue('A2', 'PILIHAN JABATAN:');
        $sheet->setCellValue('B2', 'anggota, sekretaris, ketua, bph, pembina, pengawas');
        
        $sheet->setCellValue('A3', 'PILIHAN ROLE:');
        $sheet->setCellValue('B3', 'admin, guru, anggota');
        
        $sheet->setCellValue('D3', 'KOMISI / DIVISI:');
        $sheet->setCellValue('E3', 'Nama Komisi (untuk MPK) / Divisi (untuk OSIS). Jika diisi dan belum ada, komisi/divisi otomatis terbuat.');

        // Headers at row 4
        $sheet->setCellValue('A4', 'Nama');
        $sheet->setCellValue('B4', 'Email');
        $sheet->setCellValue('C4', 'Jabatan');
        $sheet->setCellValue('D4', 'Role');
        $sheet->setCellValue('E4', 'Komisi / Divisi');
        
        // Example data at row 5 and 6
        $sheet->setCellValue('A5', 'Ahmad Dani');
        $sheet->setCellValue('B5', 'dani@skomda.sch.id');
        $sheet->setCellValue('C5', 'anggota');
        $sheet->setCellValue('D5', 'anggota');
        $sheet->setCellValue('E5', 'Komisi A');
        
        $sheet->setCellValue('A6', 'Siti Aminah');
        $sheet->setCellValue('B6', 'siti@skomda.sch.id');
        $sheet->setCellValue('C6', 'bph');
        $sheet->setCellValue('D6', 'anggota');
        $sheet->setCellValue('E6', 'Divisi Humas');

        // Style headers to look nice
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_buat_akun_semarak.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Import users from Excel and assign them as members.
     */
    public function importExcel(Request $request, Organisasi $organisasi)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5000',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Shift the first 4 rows to skip notes and headers
            array_shift($rows); // Row 1 (Notes)
            array_shift($rows); // Row 2 (Notes)
            array_shift($rows); // Row 3 (Notes)
            array_shift($rows); // Row 4 (Headers)

            $successCount = 0;
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($rows, $organisasi, &$successCount) {
                foreach ($rows as $row) {
                    $nama = trim($row[0] ?? '');
                    $email = trim($row[1] ?? '');
                    $jabatan = strtolower(trim($row[2] ?? 'anggota'));
                    $roleName = strtolower(trim($row[3] ?? 'anggota'));
                    $komisiNama = trim($row[4] ?? '');

                    if (empty($nama) || empty($email)) {
                        continue;
                    }

                    $user = User::where('email', $email)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $nama,
                            'email' => $email,
                            'role_name' => $roleName ?: 'anggota',
                            'status' => 'aktif',
                            'password' => 'password',
                            'must_change_password' => true,
                        ]);

                        $roleExists = \Illuminate\Support\Facades\DB::table('roles')->where('name', $roleName)->exists();
                        if ($roleExists) {
                            $user->assignRole($roleName);
                        } else {
                            $user->assignRole('anggota');
                        }
                    }

                    // Check if already member of organization
                    $isMember = OrganisasiMember::where('user_id', $user->id)
                        ->where('organisasi_id', $organisasi->id)
                        ->exists();

                    if (!$isMember) {
                        OrganisasiMember::create([
                            'user_id' => $user->id,
                            'organisasi_id' => $organisasi->id,
                            'jabatan' => $jabatan ?: 'anggota',
                        ]);
                        $successCount++;
                    }

                    // Map to Komisi / Divisi if specified
                    if (!empty($komisiNama)) {
                        $komisi = Komisi::firstOrCreate([
                            'nama' => $komisiNama,
                            'organisasi_id' => $organisasi->id,
                        ], [
                            'is_active' => true,
                        ]);

                        $isKomisiMember = KomisiMember::where('user_id', $user->id)
                            ->where('komisi_id', $komisi->id)
                            ->exists();

                        if (!$isKomisiMember) {
                            KomisiMember::create([
                                'user_id' => $user->id,
                                'komisi_id' => $komisi->id,
                            ]);
                        }
                    }
                }
            });

            if ($successCount > 0) {
                flash()->success("$successCount akun & anggota baru berhasil didaftarkan.");
            } else {
                flash()->info("Tidak ada anggota baru yang ditambahkan, namun penyesuaian komisi/divisi berhasil diproses.");
            }
        } catch (\Exception $e) {
            flash()->error("Gagal mengimpor file: " . $e->getMessage());
        }

        return redirect()->back();
    }
}
