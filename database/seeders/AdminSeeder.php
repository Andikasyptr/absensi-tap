<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun admin default jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@sifat.com'], // Cek berdasarkan email agar tidak duplikat
            [
                'name' => 'Administrator SIFAT',
                'password' => Hash::make('M4sukyangbener'), // Password default
            ]
        );
    }
}