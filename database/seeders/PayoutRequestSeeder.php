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
        $users = User::where('is_admin', false)->where('total_earned', '>', 5_000)->get();
        $admin = User::where('is_admin', true)->first();
        if ($users->isEmpty() || ! $admin) {
            return;
        }

        $batch = [];
        $balanceDecrements = [];

        $statusPool = [
            'pending', 'pending', 'pending',
            'paid', 'paid', 'paid', 'paid', 'paid',
            'rejected', 'rejected',
            'approved',
        ];

        foreach ($users as $u) {
            $remaining = (int) $u->balance;
            $count = fake()->numberBetween(4, 18);

            for ($i = 0; $i < $count; $i++) {
                if ($remaining < 5_000) {
                    break;
                }
                // Cap each payout to ~1/4 of remaining so users can have multiple historical payouts.
                $cap = max(5_000, min(200_000, (int) ($remaining / 3)));
                $amount = fake()->numberBetween(5_000, $cap);
                $status = fake()->randomElement($statusPool);
                $createdAt = fake()->dateTimeBetween('-90 days', '-1 hour');
                $processedAt = in_array($status, ['paid', 'approved', 'rejected'])
                    ? fake()->dateTimeBetween($createdAt, 'now')
                    : null;

                $batch[] = [
                    'user_id' => $u->id,
                    'amount' => $amount,
                    'method' => $u->payout_method ?? fake()->randomElement(['momo', 'zalo', 'paypal']),
                    'account_info' => $u->payout_account ?? fake()->phoneNumber(),
                    'status' => $status,
                    'admin_note' => $status === 'rejected' ? fake()->randomElement([
                        'Account info mismatch',
                        'Số tài khoản không đúng',
                        'Sai số điện thoại MoMo',
                        'PayPal email không nhận tiền',
                    ]) : null,
                    'transaction_ref' => $status === 'paid' ? 'TX-'.fake()->numerify('########') : null,
                    'processed_by' => $processedAt ? $admin->id : null,
                    'processed_at' => $processedAt,
                    'created_at' => $createdAt,
                    'updated_at' => $processedAt ?? $createdAt,
                ];

                if ($status !== 'rejected') {
                    $remaining -= $amount;
                    $balanceDecrements[$u->id] = ($balanceDecrements[$u->id] ?? 0) + $amount;
                }

                if (count($batch) >= 500) {
                    PayoutRequest::insert($batch);
                    $batch = [];
                }
            }
        }
        if ($batch) {
            PayoutRequest::insert($batch);
        }

        foreach ($balanceDecrements as $userId => $totalDecrement) {
            DB::table('users')->where('id', $userId)->decrement('balance', $totalDecrement);
        }
    }
}
