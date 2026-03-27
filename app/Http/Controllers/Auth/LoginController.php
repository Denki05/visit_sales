<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => 1
        ];

        if (Auth::guard('superuser')->attempt($credentials)) {
            return redirect('/prospect');
        }

        return back()->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        Auth::guard('superuser')->logout();
        return redirect('/login');
    }
}