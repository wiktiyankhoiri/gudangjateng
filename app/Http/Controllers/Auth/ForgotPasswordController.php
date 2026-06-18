<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = User::where('username', $request->input('username'))->first();

        // Generic response to prevent username enumeration
        if (!$user || !$user->email) {
            return back()->with('success', 'Jika username ditemukan dan memiliki email, link reset password akan dikirim.');
        }

        // Hapus token lama jika ada
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Buat token baru
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        // Kirim email reset password
        $user->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));

        Log::info("Reset password link sent", ['user' => $user->username, 'email' => $user->email]);

        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }
}
