<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('profil.index', [
            'title' => 'Edit Profil',
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'nama' => 'required|min:3',
            'username' => 'required|min:3|unique:users,username,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password_lama' => 'nullable|required_with:password_baru',
            'password_baru' => 'nullable|min:6|regex:/[A-Za-z]/|regex:/[0-9]/',
            'konfirmasi_password' => 'nullable|same:password_baru',
        ]);

        $passwordLama = $request->input('password_lama');
        $passwordBaru = $request->input('password_baru');

        if (!empty($passwordBaru)) {
            if (!Hash::check($passwordLama, $user->password)) {
                return redirect()->back()->with('error', 'Password lama salah');
            }

            if (Hash::check($passwordBaru, $user->password)) {
                return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password lama');
            }
        }

        $data = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? $validated['username'] . '@gudangjateng.com',
        ];

        if (!empty($passwordBaru)) {
            $data['password'] = $passwordBaru;
        }

        $user->update($data);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui');
    }
}
