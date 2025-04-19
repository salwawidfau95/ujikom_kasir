<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import Model User
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat user default.
     */
    public function run()
    {
        User::create([
            'username' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123'), // Hashing password
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('123'),
            'role' => 'staff',
        ]);
    }
}
