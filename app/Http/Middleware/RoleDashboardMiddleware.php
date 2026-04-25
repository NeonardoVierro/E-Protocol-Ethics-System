<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleDashboardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Cek status akun aktif atau tidak
        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda belum diaktivasi oleh sekretaris.');
        }
        
        // Redirect ke dashboard sesuai role
        $route = $request->route()->getName();
        
        if ($route === 'dashboard') {
            if ($user->hasRole('peneliti')) {
                return redirect()->route('peneliti.dashboard');
            } elseif ($user->hasRole('sekretaris') || $user->hasRole('ketua')) {
                return redirect()->route('sekretaris.dashboard');
            } elseif ($user->hasRole('reviewer')) {
                return redirect()->route('reviewer.dashboard');
            } elseif ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
        }
        
        return $next($request);
    }
}