<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtendSessionForCapacitor
{
    /**
     * Handle an incoming request.
     *
     * Detects if the request comes from the Capacitor Android APK
     * and extends session lifetime to 8 hours (480 minutes).
     * Web browser sessions stay at the default 2 hours (120 minutes).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isCapacitorRequest($request)) {
            config(['session.lifetime' => 480]);
        }

        return $next($request);
    }

    /**
     * Detect whether the request comes from Capacitor APK.
     * Uses User-Agent string (Capacitor adds this automatically)
     * or optional X-Capacitor header for extra reliability.
     */
    private function isCapacitorRequest(Request $request): bool
    {
        // Capacitor appends "Capacitor" to the User-Agent automatically
        if (str_contains($request->userAgent() ?? '', 'Capacitor')) {
            return true;
        }

        // Allow explicit header for extra reliability
        if ($request->header('X-Capacitor') === 'true') {
            return true;
        }

        return false;
    }
}
