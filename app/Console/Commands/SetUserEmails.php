<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserEmails extends Command
{
    protected $signature = 'users:set-emails';
    protected $description = 'Set default email untuk semua user yang belum punya email';

    public function handle()
    {
        $users = User::whereNull('email')->get();

        if ($users->isEmpty()) {
            $this->info('Semua user sudah punya email.');
            return;
        }

        $this->info("Ditemukan {$users->count()} user tanpa email:");
        $this->newLine();

        $tableData = [];
        foreach ($users as $user) {
            $defaultEmail = $user->username . '@gudangjateng.com';
            $tableData[] = [
                'ID' => $user->id,
                'Username' => $user->username,
                'Nama' => $user->nama,
                'Email Baru' => $defaultEmail,
            ];
        }

        $this->table(['ID', 'Username', 'Nama', 'Email Baru'], $tableData);
        $this->newLine();

        if ($this->confirm('Update semua user dengan email di atas?')) {
            foreach ($users as $user) {
                $defaultEmail = $user->username . '@gudangjateng.com';
                $user->update(['email' => $defaultEmail]);
                $this->line("  ✅ {$user->username} → {$defaultEmail}");
            }
            $this->newLine();
            $this->info('Semua user berhasil diupdate!');
        } else {
            $this->info('Dibatalkan.');
        }
    }
}
