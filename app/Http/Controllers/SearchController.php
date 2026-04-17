<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // fungsi untuk mencari karyawan dan departemen berdasarkan kata kunci
    public function cari(Request $request)
    {
        // ambil kata kunci dari query string
        $kata = $request->q;

        // kalau tidak ada kata kunci, kembalikan array kosong
        if (empty($kata) || strlen($kata) < 2) {
            return response()->json([]);
        }

        // cari karyawan yang namanya, emailnya, atau posisinya mengandung kata kunci
        $karyawan = User::where('name', 'like', '%' . $kata . '%')
            ->orWhere('email', 'like', '%' . $kata . '%')
            ->orWhere('position', 'like', '%' . $kata . '%')
            ->orWhere('user_id', 'like', '%' . $kata . '%')
            ->select('id', 'name', 'email', 'user_id', 'position', 'avatar', 'department')
            ->limit(6)
            ->get();

        // cari departemen yang namanya mengandung kata kunci
        $departemen = Department::where('department', 'like', '%' . $kata . '%')
            ->select('id', 'department', 'head_of')
            ->limit(3)
            ->get();

        // format hasil untuk dikirim ke frontend
        $hasil = [];

        foreach ($karyawan as $k) {
            $hasil[] = [
                'tipe'   => 'karyawan',
                'label'  => $k->name,
                'sub'    => $k->position ?? 'karyawan',
                'id'     => $k->user_id,
                'url'    => url('page/account/' . $k->user_id),
                'avatar' => $k->avatar ? asset('assets/images/user/' . $k->avatar) : null,
            ];
        }

        foreach ($departemen as $d) {
            $hasil[] = [
                'tipe'  => 'departemen',
                'label' => $d->department,
                'sub'   => 'kepala: ' . ($d->head_of ?? '-'),
                'id'    => $d->id,
                'url'   => route('hr/department/page'),
            ];
        }

        return response()->json($hasil);
    }
}

