<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterDataController extends Controller
{
    // index() → return view with all 3 tables data
    public function index()
    {
        $positions = DB::table('position_types')->get();
        $userTypes = DB::table('user_types')->get();
        $roleTypes = DB::table('role_type_users')->get();

        return view('hr.settings.master', compact('positions', 'userTypes', 'roleTypes'));
    }

    // --- Position Methods ---

    public function storePosition(Request $request)
    {
        $request->validate(['position' => 'required|string|max:100']);

        DB::table('position_types')->insert([
            'position' => $request->position,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        flash()->success('Jabatan berhasil ditambahkan.');
        return redirect()->back();
    }

    public function updatePosition(Request $request, $id)
    {
        $request->validate(['position' => 'required|string|max:100']);

        DB::table('position_types')->where('id', $id)->update([
            'position' => $request->position,
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jabatan berhasil diperbarui.']);
        }

        flash()->success('Jabatan berhasil diperbarui.');
        return redirect()->back();
    }

    public function destroyPosition($id)
    {
        $data = DB::table('position_types')->where('id', $id)->first();
        if (!$data) {
            flash()->error('Data tidak ditemukan.');
            return redirect()->back();
        }

        $isUsedInUsers = DB::table('users')->where('position', $data->position)->exists();
        $isUsedInProfiles = DB::table('employee_profiles')->where('jabatan', $data->position)->exists();

        if ($isUsedInUsers || $isUsedInProfiles) {
            flash()->error('Data masih digunakan oleh karyawan aktif.');
            return redirect()->back();
        }

        DB::table('position_types')->where('id', $id)->delete();
        flash()->success('Jabatan berhasil dihapus.');
        return redirect()->back();
    }

    // --- User Type Methods ---

    public function storeUserType(Request $request)
    {
        $request->validate(['type_name' => 'required|string|max:100']);

        DB::table('user_types')->insert([
            'type_name' => $request->type_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        flash()->success('Status karyawan berhasil ditambahkan.');
        return redirect()->back();
    }

    public function updateUserType(Request $request, $id)
    {
        $request->validate(['type_name' => 'required|string|max:100']);

        DB::table('user_types')->where('id', $id)->update([
            'type_name' => $request->type_name,
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status karyawan berhasil diperbarui.']);
        }

        flash()->success('Status karyawan berhasil diperbarui.');
        return redirect()->back();
    }

    public function destroyUserType($id)
    {
        $data = DB::table('user_types')->where('id', $id)->first();
        if (!$data) {
            flash()->error('Data tidak ditemukan.');
            return redirect()->back();
        }

        $isUsed = DB::table('users')->where('status', $data->type_name)->exists();

        if ($isUsed) {
            flash()->error('Data masih digunakan oleh karyawan aktif.');
            return redirect()->back();
        }

        DB::table('user_types')->where('id', $id)->delete();
        flash()->success('Status karyawan berhasil dihapus.');
        return redirect()->back();
    }

    // --- Role Type Methods ---

    public function storeRoleType(Request $request)
    {
        flash()->error('Role sistem bersifat tetap dan tidak dapat ditambah.');
        return redirect()->back();
    }

    public function updateRoleType(Request $request, $id)
    {
        flash()->error('Role sistem bersifat tetap dan tidak dapat diubah.');
        return redirect()->back();
    }

    public function destroyRoleType($id)
    {
        flash()->error('Role sistem bersifat tetap dan tidak dapat dihapus.');
        return redirect()->back();
    }
}
