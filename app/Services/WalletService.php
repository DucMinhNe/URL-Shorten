<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(User $user, int $amount, ?string $refType = null, ?int $refId = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $refType, $refId, $description) {
            $user = User::lockForUpdate()->find($user->id);
            $user->balance += $amount;
            $user->total_earned += $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $user->balance,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }

    public function debit(User $user, int $amount, string $type, ?int $refId = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $refId, $description) {
            $user = User::lockForUpdate()->find($user->id);
            if ($user->balance < $amount) {
                throw new \RuntimeException('Insufficient balance');
            }
            $user->balance -= $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => -$amount,
                'balance_after' => $user->balance,
                'reference_type' => $type === 'payout_hold' ? 'payout_request' : null,
                'reference_id' => $refId,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }

    public function refund(User $user, int $amount, ?int $refId = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $refId, $description) {
            $user = User::lockForUpdate()->find($user->id);
            $user->balance += $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'payout_reject',
                'amount' => $amount,
                'balance_after' => $user->balance,
                'reference_type' => 'payout_request',
                'reference_id' => $refId,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }
}
