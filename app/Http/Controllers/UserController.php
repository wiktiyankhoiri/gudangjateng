<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->requireSuperAdmin();

        $cari = $request->get('cari');

        $query = User::query();

        if ($cari) {
            $query->where(function ($q2) use ($cari) {
                $q2->where('nama', 'like', "%{$cari}%")
                   ->orWhere('username', 'like', "%{$cari}%");
            });
        }

        $data = $query->orderBy('id', 'DESC')->paginate(50);

        return view('pengaturan.management-user.index', [
            'title' => 'Management User',
            'data' => $data,
            'cari' => $cari,
        ]);
    }

    public function create()
    {
        $this->requireSuperAdmin();

        return view('pengaturan.management-user.create', ['title' => 'Tambah User']);
    }

    public function store(Request $request)
    {
        $this->requireSuperAdmin();

        $validated = $request->validate([
            'nama' => 'required|min:3',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,sales,audit,manager,super_admin,staff',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? $validated['username'] . '@gudangjateng.com',
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        $this->writeAuditLog('create', $user->id, 'User berhasil ditambahkan', [
            'nama' => $user->nama,
            'username' => $user->username,
            'role' => $user->role,
        ]);

        return redirect()->route('pengaturan.user.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $this->requireSuperAdmin();

        return view('pengaturan.management-user.edit', ['title' => 'Edit User', 'data' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $this->requireSuperAdmin();

        $validated = $request->validate([
            'nama' => 'required|min:3',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,sales,audit,manager,super_admin,staff',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? $validated['username'] . '@gudangjateng.com',
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        $this->writeAuditLog('update', $user->id, 'User berhasil diupdate', [
            'nama' => $user->nama,
            'username' => $user->username,
            'role' => $user->role,
        ]);

        return redirect()->route('pengaturan.user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $this->requireSuperAdmin();

        if ($user->id === auth()->id()) {
            return redirect()->route('pengaturan.user.index')->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        try {
            $userData = $user->toArray();
            $user->delete();

            $this->writeAuditLog('delete', $user->id, 'User berhasil dihapus', $userData);

            return redirect()->route('pengaturan.user.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pengaturan.user.index')->with('error', $this->getSafeErrorMessage($e));
        }
    }

    protected function requireAdmin(): void
    {
        $role = auth()->user()->role;
        if ($role !== 'admin' && $role !== 'super_admin') {
            abort(404);
        }
    }

    protected function writeAuditLog(string $action, $refId, string $description, array $data = []): void
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'table_name' => 'users',
                'reference_id' => $refId,
                'description' => $description,
                'data' => json_encode($data),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Audit log failed: ' . $e->getMessage());
        }
    }

    protected function getSafeErrorMessage(\Throwable $e, string $fallback = 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'): string
    {
        $msg = $e->getMessage();
        if (str_contains($msg, 'SQLSTATE')) {
            return 'Terjadi kesalahan database. Silakan coba lagi.';
        }
        return $msg;
    }
}
