<?php

namespace Database\Seeders;

use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = DB::table('clicks')
            ->join('short_links', 'clicks.short_link_id', '=', 'short_links.id')
            ->whereNotNull('short_links.user_id')
            ->where('clicks.is_valid', true)
            ->selectRaw('short_links.user_id, DATE(clicks.created_at) as d, SUM(clicks.earnings) as total')
            ->groupBy('short_links.user_id', 'd')
            ->orderBy('short_links.user_id')
            ->orderBy('d')
            ->get();

        $running = [];
        $bulk = [];
        foreach ($rows as $r) {
            $running[$r->user_id] = ($running[$r->user_id] ?? 0) + (int) $r->total;
            $bulk[] = [
                'user_id' => $r->user_id,
                'type' => 'credit',
                'amount' => (int) $r->total,
                'balance_after' => $running[$r->user_id],
                'reference_type' => 'click_batch',
                'reference_id' => null,
                'description' => "Click earnings {$r->d}",
                'created_at' => $r->d.' 23:59:59',
            ];
            if (count($bulk) >= 500) {
                WalletTransaction::insert($bulk);
                $bulk = [];
            }
        }
        if ($bulk) {
            WalletTransaction::insert($bulk);
        }

        // Payout transactions
        $payoutTx = [];
        foreach (PayoutRequest::orderBy('created_at')->get() as $pr) {
            if (in_array($pr->status, ['pending', 'approved', 'paid'])) {
                $running[$pr->user_id] = ($running[$pr->user_id] ?? 0) - $pr->amount;
                $payoutTx[] = [
                    'user_id' => $pr->user_id, 'type' => 'payout_hold',
                    'amount' => -$pr->amount, 'balance_after' => $running[$pr->user_id],
                    'reference_type' => 'payout_request', 'reference_id' => $pr->id,
                    'description' => "Hold for payout #{$pr->id}",
                    'created_at' => $pr->created_at,
                ];
                if ($pr->status === 'paid') {
                    $payoutTx[] = [
                        'user_id' => $pr->user_id, 'type' => 'payout_release',
                        'amount' => 0, 'balance_after' => $running[$pr->user_id],
                        'reference_type' => 'payout_request', 'reference_id' => $pr->id,
                        'description' => "Paid: {$pr->transaction_ref}",
                        'created_at' => $pr->processed_at,
                    ];
                }
            } elseif ($pr->status === 'rejected') {
                $payoutTx[] = [
                    'user_id' => $pr->user_id, 'type' => 'payout_reject',
                    'amount' => 0, 'balance_after' => $running[$pr->user_id] ?? 0,
                    'reference_type' => 'payout_request', 'reference_id' => $pr->id,
                    'description' => "Rejected: {$pr->admin_note}",
                    'created_at' => $pr->processed_at,
                ];
            }
        }
        if ($payoutTx) {
            WalletTransaction::insert($payoutTx);
        }
    }
}
