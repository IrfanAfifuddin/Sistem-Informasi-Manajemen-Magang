<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // 1. Bypass check if the user is NOT an intern
            if ($user->role !== 'intern') {
                return $next($request);
            }

            // 2. For interns, check if password is still the default (username holds NIM) or is_first_login
            $defaultPasswordCheck = Hash::check($user->username, $user->password);
            $firstLoginCheck = $user->is_first_login ?? false;

            if ($defaultPasswordCheck || $firstLoginCheck) {
                // 3. Allow access to password change routes and logout to prevent infinite redirect loops
                $allowedRoutes = [
                    'profile.edit',
                    'profile.update',
                    'password.update',
                    'logout'
                ];

                if (!$request->routeIs($allowedRoutes)) {
                    return redirect()->route('profile.edit')
                        ->with('warning', 'Peringatan! Anda wajib mengganti password default sebelum melanjutkan.');
                }
            }
        }

        return $next($request);
    }
}
