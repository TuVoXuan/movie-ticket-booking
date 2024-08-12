<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function getLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        try {
            $body = $request->all();

            $validated = Validator::make($body, [
                'account' => 'required|string|min:3|regex:/^[a-z][a-z0-9]*$/',
                'password' => 'required|string|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
            ]);
            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $credentials = $request->only(['account', 'password']);
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->route('dashboard');
            }

            return back()->withErrors([
                'account' => 'The provided credentials do not match our records.',
            ])->onlyInput('account');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('auth.getLogin')->with('error', 'An error occurred during login.');
        }
    }

    public function Logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('auth.getLogin');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'An error occurred during logout.');
        }
    }
}
