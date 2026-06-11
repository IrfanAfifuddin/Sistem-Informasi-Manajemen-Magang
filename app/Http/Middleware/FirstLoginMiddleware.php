<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirstLoginMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_first_login) {
                // Allow all GET requests so they can browse dashboard and profile
                if ($request->isMethod('GET')) {
                    return $next($request);
                }

                // Block any other POST/write actions except password update and logout
                if (!$request->routeIs('password.update', 'logout')) {
                    return redirect()->route('profile.edit')
                        ->with('warning', 'Anda harus mengubah password default terlebih dahulu di halaman profil sebelum melakukan aksi.');
                }
            }
        }

        return $next($request);
    }
}
