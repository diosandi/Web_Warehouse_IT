<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin default
        User::create([
            'name' => 'Admin',
            'email' => 'admin@rscm.co.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}

