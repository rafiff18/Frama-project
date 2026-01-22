<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat Superadmin
        User::create([
            'name' => 'Superadmin',
            'email' => 'super@cafe.com',
            'password' => Hash::make('super123'),
            'role' => 'superadmin',
        ]);

        // Membuat Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@cafe.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Membuat Chef
        User::create([
            'name' => 'Chef User',
            'email' => 'chef@cafe.com',
            'password' => Hash::make('chef123'),
            'role' => 'chef',
        ]);

        // Membuat Kasir
        User::create([
            'name' => 'Kasir User',
            'email' => 'kasir@cafe.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        // Membuat Owner
        User::create([
            'name' => 'Owner User',
            'email' => 'owner@cafe.com',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
        ]);
    }
}