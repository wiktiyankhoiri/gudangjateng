<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
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
        $isCapacitor = $this->isCapacitorRequest($request);

        if ($isCapacitor) {
            config(['session.lifetime' => 480]);
        }

        /** @var Response $response */
        $response = $next($request);

        // Set cookie dari SERVER agar request berikutnya langsung terdeteksi.
        // Cookie expired 31 hari — supaya APK ga perlu detect ulang.
        if ($isCapacitor && !$request->cookie('capacitor_app')) {
            $response->cookie('capacitor_app', '1', 44640, '/', null, false, false); // 31 hari
        }

        return $response;
    }

    /**
     * Detect whether the request comes from Capacitor APK.
     * Priority:
     * 1. Cookie 'capacitor_app' (diset server di request sebelumnya, atau fallback JS)
     * 2. Header X-Capacitor (via axios/fetch dari JS)
     * 3. User-Agent mengandung 'Capacitor' (fallback — dipake di request pertama)
     */
    private function isCapacitorRequest(Request $request): bool
    {
        // Cookie — paling reliable, diset oleh server untuk request selanjutnya
        if ($request->cookie('capacitor_app') === '1') {
            return true;
        }

        // Header X-Capacitor — dikirim axios/fetch dari JS
        if ($request->header('X-Capacitor') === 'true') {
            return true;
        }

        // User-Agent — Capacitor nambahin "Capacitor" di User-Agent secara default
        $ua = $request->userAgent() ?? '';
        if (str_contains($ua, 'Capacitor') || str_contains($ua, 'capacitor')) {
            return true;
        }

        return false;
    }
}
