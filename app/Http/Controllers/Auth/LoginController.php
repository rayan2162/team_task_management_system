<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers;

    /**
     * Redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return '/'; // ✅ redirect to home instead of /home
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle tasks after user is authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        // ❌ If user is NOT approved
        if (!$user->is_approved) {
            $this->guard()->logout();

            return redirect('/')
                ->withErrors([
                    'email' => 'Your account is not approved yet. Please wait for admin approval.'
                ]);
        }

        // ✅ If approved, continue normally
        return redirect($this->redirectTo());
    }
}