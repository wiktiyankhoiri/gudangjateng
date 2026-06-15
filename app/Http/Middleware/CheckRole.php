<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // Super Admin bypass semua role check
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        if (!in_array($userRole, $roles, true)) {
            return redirect()->route('beranda')
                ->with('error', 'Anda tidak memiliki akses');
        }

        return $next($request);
    }
}
