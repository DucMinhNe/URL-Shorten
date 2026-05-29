<?php

namespace App\Services;

use App\Models\PayoutRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PayoutService
{
    public function __construct(private WalletService $wallet) {}

    public function createRequest(User $user, int $amount, string $method, string $accountInfo): PayoutRequest
    {
        return DB::transaction(function () use ($user, $amount, $method, $accountInfo) {
            $request = PayoutRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'method' => $method,
                'account_info' => $accountInfo,
                'status' => 'pending',
            ]);
            $this->wallet->debit($user, $amount, 'payout_hold', $request->id, "Payout #{$request->id}");
            return $request->fresh();
        });
    }

    public function markPaid(PayoutRequest $request, User $admin, string $txRef): void
    {
        DB::transaction(function () use ($request, $admin, $txRef) {
            $req = PayoutRequest::lockForUpdate()->findOrFail($request->id);
            if ($req->status !== 'pending') {
                throw new RuntimeException("Yêu cầu đã được xử lý (status: {$req->status}).");
            }

            $req->update([
                'status' => 'paid',
                'processed_by' => $admin->id,
                'processed_at' => now(),
                'transaction_ref' => $txRef,
            ]);

            $user = User::find($req->user_id);
            WalletTransaction::create([
                'user_id' => $req->user_id,
                'type' => 'payout_release',
                'amount' => 0,
                'balance_after' => $user?->balance ?? 0,
                'reference_type' => 'payout_request',
                'reference_id' => $req->id,
                'description' => "Paid via {$req->method}: {$txRef}",
                'created_at' => now(),
            ]);
        });
    }

    public function reject(PayoutRequest $request, User $admin, string $reason): void
    {
        DB::transaction(function () use ($request, $admin, $reason) {
            $req = PayoutRequest::lockForUpdate()->findOrFail($request->id);
            if ($req->status !== 'pending') {
                throw new RuntimeException("Yêu cầu đã được xử lý (status: {$req->status}).");
            }

            $req->update([
                'status' => 'rejected',
                'processed_by' => $admin->id,
                'processed_at' => now(),
                'admin_note' => $reason,
            ]);
            $this->wallet->refund(
                $req->user,
                $req->amount,
                $req->id,
                "Refund payout #{$req->id}: {$reason}"
            );
        });
    }
}
