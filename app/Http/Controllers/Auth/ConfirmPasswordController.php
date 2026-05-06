<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | confirm password controller
    |--------------------------------------------------------------------------
    |
    | this controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. you're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
