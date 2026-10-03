<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['username' => 'pemilik@rumahmakan.com'],
            [
                'name' => 'pemilik',
                'email' => 'rm.bismillah.pattimura@gmail.com',
                'password' => Hash::make('pemilik123'),
                'role' => 'pemilik',
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin@rumahmakan.com'],
            [
                'name' => 'admin',
                'email' => 'rm.bismillah.pattimura@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
    }
}
