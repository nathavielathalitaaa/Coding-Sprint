<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | password reset controller
    |--------------------------------------------------------------------------
    |
    | this controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. you're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
