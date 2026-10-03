<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class PengeluaranMingguanSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil ID Kategori Pengeluaran yang VALID (Tipe pengeluaran)
        $category = Category::where('type', 'pengeluaran')->first();
        
        // 2. Ambil ID User yang VALID (Admin atau Pemilik yang ada)
        $user = User::first();

        // Cek jika kategori atau user tidak ada, jangan jalankan seeder agar tidak error
        if (!$category || !$user) {
            return;
        }

        $items = ['Ayam kota', 'Daging sapi', 'Ikan nila', 'Bumbu dapur', 'Minyak goreng', 'Gas elpiji', 'Sayur-sayuran', 'Ikan laut', 'Jeroan'];

        // Kosongkan data lama agar bersih
        Expense::truncate();

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            
            for ($j = 0; $j < rand(1, 2); $j++) {
                Expense::create([
                    'description' => $items[array_rand($items)],
                    'expense_date' => $date->format('Y-m-d'),
                    'amount' => rand(20000, 100000),
                    'category_id' => $category->id, // Mengambil ID otomatis dari tabel kategori
                    'user_id' => $user->id,         // Mengambil ID otomatis dari tabel user
                ]);
            }
        }
    }
}