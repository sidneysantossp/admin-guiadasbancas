<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:distributor', ['except' => ['logout']]);
    }

    /**
     * Show the login form
     */
    public function login()
    {
        return view('distributor-views.auth.login');
    }

    /**
     * Handle login submission
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1; // Only active distributors
        $credentials['distributor'] = 1; // Only distributors

        if (Auth::guard('distributor')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('distributor.dashboard'))
                ->with('success', translate('messages.login_successful'));
        }

        return redirect()->back()
            ->withErrors(['email' => translate('messages.invalid_credentials')])
            ->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('distributor')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')
            ->with('success', translate('messages.logout_successful'));
    }
}