<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => Hash::make('Admin@123'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'balance' => 0,
            'status' => 'active',
        ]);
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@demo.com',
            'password' => Hash::make('Demo@123'),
            'email_verified_at' => now(),
            'balance' => 250_000,
            'total_earned' => 1_800_000,
            'payout_method' => 'momo',
            'payout_account' => '0901234567',
            'status' => 'active',
        ]);
        User::factory()->count(48)->create();
    }
}
