<?php

namespace Database\Seeders;

use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Threshold lowered to ensure ~28 qualifying users with demo seed scale.
        $users = User::where('is_admin', false)->where('total_earned', '>', 30000)->get();
        $admin = User::where('is_admin', true)->first();
        if ($users->isEmpty() || ! $admin) {
            return;
        }

        foreach ($users->random(min(28, $users->count())) as $u) {
            $statuses = ['pending', 'pending', 'pending', 'paid', 'paid', 'paid', 'rejected', 'rejected', 'approved'];
            $status = fake()->randomElement($statuses);
            // Cap amount to user's actual balance to avoid negative wallet balance (unsigned column).
            $balance = (int) $u->balance;
            if ($balance < 20_000) {
                continue;
            }
            $amount = fake()->numberBetween(20_000, min(500_000, $balance));
            $createdAt = fake()->dateTimeBetween('-60 days', '-1 day');
            $processedAt = in_array($status, ['paid', 'approved', 'rejected']) ? fake()->dateTimeBetween($createdAt, 'now') : null;

            PayoutRequest::create([
                'user_id' => $u->id,
                'amount' => $amount,
                'method' => $u->payout_method ?? 'momo',
                'account_info' => $u->payout_account ?? fake()->phoneNumber(),
                'status' => $status,
                'admin_note' => $status === 'rejected' ? 'Account info mismatch' : null,
                'transaction_ref' => $status === 'paid' ? 'TX-'.fake()->numerify('######') : null,
                'processed_by' => $processedAt ? $admin->id : null,
                'processed_at' => $processedAt,
                'created_at' => $createdAt,
                'updated_at' => $processedAt ?? $createdAt,
            ]);

            if ($status !== 'rejected') {
                DB::table('users')->where('id', $u->id)->decrement('balance', $amount);
            }
        }
    }
}
