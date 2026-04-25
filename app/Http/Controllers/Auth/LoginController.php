<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Cek status akun
            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda belum diaktivasi. Silakan hubungi sekretaris.',
                ]);
            }
            
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            if ($user->hasRole('peneliti')) {
                return redirect()->intended(route('peneliti.dashboard'));
            } elseif ($user->hasRole('sekretaris') || $user->hasRole('ketua')) {
                return redirect()->intended(route('sekretaris.dashboard'));
            } elseif ($user->hasRole('reviewer')) {
                return redirect()->intended(route('reviewer.dashboard'));
            } elseif ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}