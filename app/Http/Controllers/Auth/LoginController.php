<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /** show the login page */
    public function login()
    {
        return view('auth.login');
    }

    /** authenticate the user */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $todayDate = Carbon::now()->toDayDateTimeString();

                // store user information in session
                Session::put([
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'user_id'      => $user->user_id,
                    'join_date'    => $user->join_date,
                    'last_login'   => $todayDate,
                    'phone_number' => $user->phone_number,
                    'location'     => $user->location,
                    'status'       => $user->status,
                    'role_name'    => $user->role_name,
                    'avatar'       => $user->avatar,
                    'position'     => $user->position,
                    'department'   => $user->department,
                ]);
                
                // update last login
                $user->update(['last_login' => $todayDate]);

                flash()->success('Login successful :)');
                
                // redirect berdasarkan role
                if ($user->hasRole('hr')) {
                    return redirect()->route('home');
                }
                
                return redirect()->route('surat.index');
            } else {
                flash()->error('Error: Wrong username or password :)');
                return redirect('login');
            }
        } catch (\Exception $e) {
            \Log::error($e);
            flash()->error('An error occurred during login :)');
            return redirect()->back();
        }
    }

    /** show logout page */
    public function logoutPage()
    {
        return view('auth.logout');
    }

    /** logout and forget session */
    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        flash()->success('Logout successful :)');
        return redirect('logout/page');
    }
}