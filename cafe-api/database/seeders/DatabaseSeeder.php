<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@cafe.com',
            'password' => Hash::make('super123'), // Kita hash manual disini
            'role' => 'superadmin',
        ]);

        // 2. Admin
        User::create([
            'name' => 'Admin Operasional',
            'email' => 'admin@cafe.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 3. Chef
        User::create([
            'name' => 'Juna Chef',
            'email' => 'chef@cafe.com',
            'password' => Hash::make('chef123'),
            'role' => 'chef',
        ]);

        // 4. Kasir
        User::create([
            'name' => 'Kasir Andalan',
            'email' => 'kasir@cafe.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        // 5. Owner
        User::create([
            'name' => 'Pak Bos',
            'email' => 'owner@cafe.com',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
        ]);
    }
}