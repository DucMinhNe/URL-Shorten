<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\PayoutRequest;
use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first();
        if (! $admin) {
            return;
        }

        $actions = [
            ['action' => 'user.banned',           'severity' => 'high',     'msg' => 'Banned user for spam'],
            ['action' => 'user.unbanned',         'severity' => 'medium',   'msg' => 'Unbanned user after review'],
            ['action' => 'user.role_changed',     'severity' => 'high',     'msg' => 'Promoted to VIP Creator'],
            ['action' => 'payout.approved',       'severity' => 'medium',   'msg' => 'Approved payout'],
            ['action' => 'payout.paid',           'severity' => 'medium',   'msg' => 'Marked as paid via Momo'],
            ['action' => 'payout.rejected',       'severity' => 'medium',   'msg' => 'Rejected — wrong account info'],
            ['action' => 'link.disabled',         'severity' => 'medium',   'msg' => 'Disabled link due to abuse'],
            ['action' => 'link.deleted',          'severity' => 'high',     'msg' => 'Deleted policy-violating link'],
            ['action' => 'report.confirmed',      'severity' => 'high',     'msg' => 'Confirmed malware report'],
            ['action' => 'report.dismissed',      'severity' => 'low',      'msg' => 'Dismissed report — not abusive'],
            ['action' => 'settings.changed',      'severity' => 'critical', 'msg' => 'Changed rate_per_1000_views'],
            ['action' => 'announcement.created',  'severity' => 'low',      'msg' => 'Published announcement'],
            ['action' => 'promo_code.created',    'severity' => 'medium',   'msg' => 'Created promo code'],
            ['action' => 'blacklist.added',       'severity' => 'high',     'msg' => 'Added domain to blacklist'],
            ['action' => 'ad_campaign.updated',   'severity' => 'medium',   'msg' => 'Updated ad campaign weight'],
            ['action' => 'admin.login',           'severity' => 'low',      'msg' => 'Admin signed in'],
            ['action' => 'admin.logout',          'severity' => 'low',      'msg' => 'Admin signed out'],
            ['action' => 'ticket.replied',        'severity' => 'low',      'msg' => 'Replied to ticket'],
            ['action' => 'ticket.resolved',       'severity' => 'low',      'msg' => 'Marked ticket as resolved'],
            ['action' => 'email_template.updated','severity' => 'low',      'msg' => 'Updated email template'],
        ];

        $userIds = User::where('is_admin', false)->pluck('id')->toArray();
        $linkIds = ShortLink::pluck('id')->toArray();
        $payoutIds = PayoutRequest::pluck('id')->toArray();

        $bulk = [];
        for ($i = 0; $i < 1500; $i++) {
            $a = fake()->randomElement($actions);
            [$targetType, $targetId, $targetLabel] = $this->pickTarget($a['action'], $userIds, $linkIds, $payoutIds);

            $bulk[] = [
                'user_id' => $admin->id,
                'user_email' => $admin->email,
                'action' => $a['action'],
                'target_type' => $targetType,
                'target_id' => $targetId,
                'target_label' => $targetLabel,
                'old_values' => $a['action'] === 'user.banned'
                    ? json_encode(['status' => 'active'])
                    : ($a['action'] === 'settings.changed' ? json_encode(['rate_per_1000_views' => 4000]) : null),
                'new_values' => $a['action'] === 'user.banned'
                    ? json_encode(['status' => 'banned'])
                    : ($a['action'] === 'settings.changed' ? json_encode(['rate_per_1000_views' => 5000]) : null),
                'severity' => $a['severity'],
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'created_at' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d H:i:s'),
            ];

            if (count($bulk) >= 500) {
                AuditLog::insert($bulk);
                $bulk = [];
            }
        }
        if ($bulk) {
            AuditLog::insert($bulk);
        }
    }

    private function pickTarget(string $action, array $userIds, array $linkIds, array $payoutIds): array
    {
        if (str_starts_with($action, 'user.')) {
            $id = fake()->randomElement($userIds);
            return ['App\\Models\\User', $id, 'user#'.$id];
        }
        if (str_starts_with($action, 'link.')) {
            $id = fake()->randomElement($linkIds);
            return ['App\\Models\\ShortLink', $id, 'link#'.$id];
        }
        if (str_starts_with($action, 'payout.')) {
            $id = $payoutIds ? fake()->randomElement($payoutIds) : null;
            return ['App\\Models\\PayoutRequest', $id, 'payout#'.($id ?? '?')];
        }
        return [null, null, null];
    }
}
