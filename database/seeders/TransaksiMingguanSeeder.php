<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use Carbon\Carbon;

class TransaksiMingguanSeeder extends Seeder
{
    public function run()
    {
        // Loop untuk 7 hari ke belakang
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Buat 3-5 transaksi per hari agar data terlihat variatif
            $jumlahTransaksi = rand(80, 100);
            
            for ($j = 0; $j < $jumlahTransaksi; $j++) {
                Transaction::create([
                    'customer_number' => 'CUST-' . rand(1, 100),
                    'transaction_date' => $date->format('Y-m-d H:i:s'),
                    'total_amount' => rand(2000, 13000), // Nominal acak
                    'user_id' => 1, // Sesuaikan dengan ID Admin Anda
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}