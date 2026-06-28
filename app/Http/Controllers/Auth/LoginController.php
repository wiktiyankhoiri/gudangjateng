<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('beranda');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Brute force protection
        $maxAttempts = 5;
        $lockoutMinutes = 15;

        $attempts = (int) session('login_attempts', 0);
        $blockedUntil = (int) session('login_blocked_until', 0);

        if ($blockedUntil > time()) {
            $remaining = ceil(($blockedUntil - time()) / 60);
            return redirect()->back()
                ->with('error', "Akun diblokir sementara. Coba lagi dalam {$remaining} menit.");
        }

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Reset login attempts
            session()->forget('login_attempts');
            session()->forget('login_blocked_until');

            $user = Auth::user();

            // Audit log
            try {
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'login',
                    'table_name' => 'users',
                    'reference_id' => $user->id,
                    'description' => 'Login berhasil',
                    'data' => json_encode(['ip' => $request->ip(), 'user_agent' => $request->userAgent()]),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                \Log::error('Gagal menulis audit log login: ' . $e->getMessage());
            }

            return redirect()->intended(route('beranda'));
        }

        // Failed login
        $attempts++;
        session(['login_attempts' => $attempts]);
        \Log::warning("Login failed: username={$credentials['username']}, attempt={$attempts}/{$maxAttempts}");

        if ($attempts >= $maxAttempts) {
            session(['login_blocked_until' => time() + ($lockoutMinutes * 60)]);
            session(['login_attempts' => 0]);
            \Log::warning("Login blocked: username={$credentials['username']}, duration={$lockoutMinutes}min");
            return redirect()->back()
                ->with('error', "Terlalu banyak percobaan gagal. Akun diblokir {$lockoutMinutes} menit.");
        }

        return redirect()->back()
            ->with('error', 'Username atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
