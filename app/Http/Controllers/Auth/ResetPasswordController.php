<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = $request->input('email');
        $token = $request->input('token');

        // Cari token di database
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetToken) {
            return back()->with('error', 'Token reset password tidak valid');
        }

        // Validasi hash token
        if (hash('sha256', $token) !== $resetToken->token) {
            return back()->with('error', 'Token reset password tidak valid');
        }

        // Cek apakah token sudah expired (60 menit)
        $createdAt = \Carbon\Carbon::parse($resetToken->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->with('error', 'Token reset password sudah expired. Silakan request ulang.');
        }

        // Update password user
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        $user->update([
            'password' => $request->input('password'),
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
