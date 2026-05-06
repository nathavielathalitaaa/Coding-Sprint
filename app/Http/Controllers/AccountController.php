<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmployeeProfile;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /** page account profile - read only (old) */
    

    public function profileDetail($user_id)
{
    $user    = User::where('user_id', $user_id)->first();
    $profile = $user?->profile()->firstOrCreate(['user_id' => $user->id ?? 0]);
    return view('pages.account-profile', compact('user', 'profile'));
}

    /** show current user's own profile */
    public function showProfile()
    {
        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        
        return view('pages.account-profile', compact('user', 'profile'));
    }

    /** update basic profile info */
    public function updateProfile(Request $request, $id = null)
    {
        $id = $id ?? Auth::id();
        $user = User::findOrFail($id);

        // security check: only hr or the user themselves can update
        if (Auth::id() != $id && !Auth::user()->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        flash()->success('Profil berhasil diperbarui');
        return redirect()->back(); // redirect back to keep context (own profile or employee profile)
    }
    
    /** update profile photo */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            // simpan ke public/assets/images/user/
            $image->move(public_path('assets/images/user'), $filename);
            
            // hapus foto lama jika ada
            if ($user->avatar && file_exists(public_path('assets/images/user/' . $user->avatar))) {
                @unlink(public_path('assets/images/user/' . $user->avatar));
            }
            
            $user->update(['avatar' => $filename]);
            
            return response()->json([
                'success' => true,
                'url' => asset('assets/images/user/' . $filename)
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Gagal upload foto'], 400);
    }

    /** upload ttd image */
    public function uploadTtd(Request $request)
    {
        $request->validate([
            'ttd' => 'required|image|mimes:png|max:2048|dimensions:min_width=300,min_height=100,max_width=1000,max_height=400',
        ], [
            'ttd.required'   => 'File tanda tangan harus diunggah',
            'ttd.image'      => 'File harus berupa gambar',
            'ttd.mimes'      => 'Format file harus PNG (transparan). JPG/JPEG tidak didukung karena tidak support transparansi.',
            'ttd.max'        => 'Ukuran file maksimal 2MB',
            'ttd.dimensions' => 'Resolusi gambar harus minimal 300×100 px dan maksimal 1000×400 px (landscape).',
        ]);

        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // ensure directory exists
        Storage::makeDirectory('private/ttd');

        // always save as png
        $filename = 'ttd/' . $user->id . '.png';

        // delete old ttd file if exists
        if ($profile->ttd_path) {
            Storage::disk('local')->delete('private/' . $profile->ttd_path);
        }

        // store new ttd file
        Storage::disk('local')->putFileAs('private', $request->file('ttd'), $filename);
        $profile->update(['ttd_path' => $filename]);

        flash()->success('Tanda tangan berhasil diunggah');
        return redirect()->route('profile.show');
    }

    /** upload digital signature (public storage) */
    public function uploadSignature(Request $request, $id = null)
    {
        $id = $id ?? Auth::id();
        $user = User::findOrFail($id);

        if (Auth::id() != $id && !Auth::user()->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'signature.required' => 'File tanda tangan wajib diunggah',
            'signature.image' => 'File harus berupa gambar',
            'signature.mimes' => 'Format file yang didukung: JPG, JPEG, PNG',
            'signature.max' => 'Ukuran maksimal file 2MB',
        ]);

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('signature')) {
            // delete old signature if exists
            if ($profile->signature_path) {
                Storage::disk('public')->delete($profile->signature_path);
            }

            // save new signature
            $path = $request->file('signature')->store('signatures', 'public');
            $profile->update(['signature_path' => $path]);
        }

        flash()->success('Tanda tangan digital berhasil disimpan');
        return redirect()->back();
    }

    /** delete digital signature (public storage) */
    public function deleteSignature($id = null)
    {
        $id = $id ?? Auth::id();
        $user = User::findOrFail($id);

        if (Auth::id() != $id && !Auth::user()->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        $profile = $user->profile;

        if ($profile && $profile->signature_path) {
            Storage::disk('public')->delete($profile->signature_path);
            $profile->update(['signature_path' => null]);
            flash()->success('Tanda tangan digital berhasil dihapus');
        } else {
            flash()->error('Tanda tangan tidak ditemukan');
        }

        return redirect()->back();
    }

    /** set or change pin */
    public function setPin(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        $request->validate([
            'pin' => 'required|digits:6',
            'pin_confirmation' => 'required|same:pin',
            'current_pin' => $profile->pin ? 'required' : 'nullable',
        ], [
            'pin.required' => 'PIN baru harus diisi',
            'pin.digits' => 'PIN harus terdiri dari 6 digit angka',
            'pin_confirmation.required' => 'Konfirmasi PIN harus diisi',
            'pin_confirmation.same' => 'Konfirmasi PIN tidak cocok',
            'current_pin.required' => 'PIN lama harus diisi',
        ]);

        // if user already has pin, verify current pin
        if ($profile->pin) {
            if (!$profile->checkPin($request->current_pin)) {
                return back()->withErrors(['current_pin' => 'PIN lama tidak sesuai']);
            }
        }

        // set new pin
        $profile->setPin($request->pin);

        flash()->success('PIN approval berhasil diatur');
        return redirect()->route('profile.show');
    }

    /** serve ttd image securely */
    public function showTtd()
    {
        $profile = Auth::user()->profile;

        if (!$profile || !$profile->ttd_path) {
            abort(404);
        }

        // correctly handle the path via storage facade to avoid double 'private' issues
        $path = Storage::disk('local')->path('private/' . $profile->ttd_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    /** update email */
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required',
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh akun lain',
            'password.required' => 'Password diperlukan untuk verifikasi identitas',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah']);
        }

        $user->update(['email' => $request->email]);

        flash()->success('Email berhasil diperbarui');
        return redirect()->route('profile.show');
    }

    /** update password */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        flash()->success('Password berhasil diperbarui');
        return redirect()->route('profile.show');
    }

    /** show onboarding page */
    public function showOnboarding()
    {
        $user    = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $step    = !$profile->ttd_path ? 'ttd' : (!$profile->pin ? 'pin' : 'done');

        if ($step === 'done') return redirect()->route('home');

        return view('pages.onboarding', compact('user', 'profile', 'step'));
    }

    /** upload ttd during onboarding */
    public function onboardingTtd(Request $request)
    {
        $request->validate([
            'ttd' => 'required|image|mimes:png|max:2048',
        ], [
            'ttd.required' => 'Tanda tangan wajib diunggah',
            'ttd.mimes'    => 'File harus berformat PNG',
            'ttd.max'      => 'Ukuran file maksimal 2MB',
        ]);

        $user    = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $ext      = 'png';
        $filename = $user->id . '.' . $ext;

        Storage::makeDirectory('private/ttd');

        if ($profile->ttd_path) {
            Storage::delete('private/' . $profile->ttd_path);
        }

        $request->file('ttd')->storeAs('private/ttd', $filename);
        $profile->update(['ttd_path' => 'ttd/' . $filename]);

        flash()->success('Tanda tangan berhasil disimpan. Sekarang buat PIN Anda.');
        return redirect()->route('onboarding');
    }

    /** set pin during onboarding */
    public function onboardingPin(Request $request)
    {
        $request->validate([
            'pin'              => 'required|digits:6',
            'pin_confirmation' => 'required|same:pin',
        ], [
            'pin.required'              => 'PIN wajib diisi',
            'pin.digits'                => 'PIN harus 6 digit angka',
            'pin_confirmation.required' => 'Konfirmasi PIN wajib diisi',
            'pin_confirmation.same'     => 'Konfirmasi PIN tidak cocok',
        ]);

        $user    = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $profile->setPin($request->pin);

        flash()->success('PIN berhasil dibuat. Selamat datang di sistem HRIS Sinergi!');
        return redirect()->route('home');
    }
}
