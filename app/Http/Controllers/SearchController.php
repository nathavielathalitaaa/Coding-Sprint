<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // cari karyawan & departemen berdasarkan keyword, return json utk autocomplete/search dropdown di frontend
    public function cari(Request $request)
    {
        // ambil keyword dr query string parameter 'q'
        $kata = $request->q;

        // klo keyword kosong atau kurang dr 2 karakter, langsung return array kosong biar gk berat query-nya
        if (empty($kata) || strlen($kata) < 2) {
            return response()->json([]);
        }

        // query cari user yg name/email/position/user_id-nya mengandung keyword, ambil 6 hasil teratas dgn field spesifik
        $karyawan = User::where('name', 'like', '%' . $kata . '%')
            ->orWhere('email', 'like', '%' . $kata . '%')
            ->orWhere('position', 'like', '%' . $kata . '%')
            ->orWhere('user_id', 'like', '%' . $kata . '%')
            ->select('id', 'name', 'email', 'user_id', 'position', 'avatar', 'department')
            ->limit(6)
            ->get();

        // format hasil search jd array dgn struktur yg bs langsung dipake frontend utk display search result
        $hasil = [];

        // loop tiap hasil user, transform jd format standar: tipe, label, subtext, id, url, & avatar
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

        return response()->json($hasil);
    }
}
