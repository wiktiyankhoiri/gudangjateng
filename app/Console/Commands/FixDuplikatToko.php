<?php

namespace App\Console\Commands;

use App\Models\Toko;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplikatToko extends Command
{
    protected $signature = 'fix:duplikat-toko';
    protected $description = 'Bersihkan data kode_toko duplikat, pertahankan yang paling lama';

    public function handle()
    {
        $this->info('Mencari duplikat kode_toko (case-insensitive)...');

        $duplicates = DB::select("
            SELECT LOWER(kode_toko) as kode, COUNT(*) as total
            FROM toko
            GROUP BY LOWER(kode_toko)
            HAVING COUNT(*) > 1
        ");

        if (empty($duplicates)) {
            $this->info('Tidak ada duplikat ditemukan.');
            return Command::SUCCESS;
        }

        $this->warn('Ditemukan ' . count($duplicates) . ' grup duplikat:');
        $totalDeleted = 0;

        foreach ($duplicates as $dup) {
            $this->line("  - {$dup->kode} ({$dup->total} record)");

            $records = Toko::whereRaw('LOWER(kode_toko) = ?', [$dup->kode])
                ->orderBy('id', 'ASC')
                ->get();

            $keep = $records->first(); // yang paling lama (ID terkecil)
            $toDelete = $records->slice(1); // sisanya dihapus

            foreach ($toDelete as $del) {
                $this->line("    → Merge ID {$del->id} ke ID {$keep->id}");

                // Update relasi barang_masuk
                DB::table('barang_masuk')
                    ->where('toko_id', $del->id)
                    ->update(['toko_id' => $keep->id]);

                // Update relasi barang_keluar
                DB::table('barang_keluar')
                    ->where('toko_id', $del->id)
                    ->update(['toko_id' => $keep->id]);

                // Hapus duplikat
                $del->forceDelete();
                $totalDeleted++;
            }
        }

        $this->info("Selesai! {$totalDeleted} data duplikat dibersihkan.");
        $this->warn('Jalankan php artisan fix:duplikat-toko lagi untuk verifikasi.');

        return Command::SUCCESS;
    }
}
