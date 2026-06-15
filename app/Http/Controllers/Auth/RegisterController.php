<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $nama = trim($request->input('nama'));
        $username = trim($request->input('username'));
        $email = trim($request->input('email'));
        $password = trim($request->input('password'));
        $role = trim($request->input('role'));

        if (!$role) {
            $role = 'sales';
        }

        $validRoles = ['admin', 'sales', 'audit', 'manager', 'super_admin'];
        if (!in_array($role, $validRoles, true)) {
            return redirect()->back()->with('error', 'Role tidak valid');
        }

        // Only admin can create admin
        if ($role === 'admin' && (!Auth::check() || Auth::user()->role !== 'admin')) {
            $role = 'sales';
        }

        if (!$nama || !$username || !$password) {
            return redirect()->back()->with('error', 'Semua field wajib diisi');
        }

        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter');
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return redirect()->back()->with('error', 'Password harus mengandung huruf dan angka');
        }

        if (User::where('username', $username)->exists()) {
            return redirect()->back()->with('error', 'Username sudah digunakan');
        }

        if ($email && User::where('email', $email)->exists()) {
            return redirect()->back()->with('error', 'Email sudah digunakan');
        }

        User::create([
            'nama' => $nama,
            'username' => $username,
            'email' => $email ?: $username . '@gudangjateng.com',
            'password' => $password,
            'role' => $role,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil');
    }
}
