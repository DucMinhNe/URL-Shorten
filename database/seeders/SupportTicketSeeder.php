<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->inRandomOrder()->limit(60)->get();
        $admins = User::where('is_admin', true)->pluck('id')->toArray();

        $subjects = [
            'Không nhận được tiền rút Momo' => 'payout',
            'Link bị 410 đột ngột' => 'link_issue',
            'Đổi email được không?' => 'account',
            'Báo cáo user farm click' => 'fraud_report',
            'Có thể thêm tính năng A/B test link?' => 'feature_request',
            'Chart dashboard không load' => 'bug',
            'PayPal email sai, cần đổi' => 'payout',
            'Quên mật khẩu, reset không tới mail' => 'account',
            'Sao click hợp lệ rate thấp vậy?' => 'other',
            'Link có thể đặt expiry không?' => 'feature_request',
            'Total earned bị âm' => 'bug',
            'Có khuyến mãi gì cho user mới ko?' => 'other',
            'Self-click bị mất tiền sao gọi là hợp lệ?' => 'fraud_report',
            'Tôi muốn rút USD nhưng chưa thấy PayPal' => 'payout',
            'Link bị mạng X chặn, làm sao?' => 'link_issue',
        ];

        $statuses = ['open', 'open', 'in_progress', 'in_progress', 'waiting_user', 'resolved', 'resolved', 'closed'];
        $priorities = ['low', 'normal', 'normal', 'normal', 'high', 'urgent'];

        foreach ($users as $u) {
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $subject = fake()->randomElement(array_keys($subjects));
                $category = $subjects[$subject];
                $status = fake()->randomElement($statuses);
                $priority = fake()->randomElement($priorities);
                $createdAt = fake()->dateTimeBetween('-45 days', '-1 hour');

                $assigned = in_array($status, ['in_progress', 'waiting_user', 'resolved', 'closed'])
                    ? fake()->randomElement($admins) : null;
                $resolvedAt = in_array($status, ['resolved', 'closed'])
                    ? fake()->dateTimeBetween($createdAt, 'now') : null;

                $ticket = SupportTicket::create([
                    'ticket_code' => 'TKT-'.strtoupper(Str::random(6)),
                    'user_id' => $u->id,
                    'subject' => $subject,
                    'category' => $category,
                    'priority' => $priority,
                    'status' => $status,
                    'assigned_to' => $assigned,
                    'resolved_at' => $resolvedAt,
                    'created_at' => $createdAt,
                    'updated_at' => $resolvedAt ?? $createdAt,
                ]);

                // First message from user
                $msgTs = $createdAt;
                SupportTicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $u->id,
                    'body' => fake()->paragraph(3),
                    'is_staff' => false,
                    'created_at' => $msgTs,
                    'updated_at' => $msgTs,
                ]);
                $replyCount = 1;
                $lastReplyAt = $msgTs;
                $lastReplyBy = 'user';

                // Replies
                $replyTotal = fake()->numberBetween(0, 5);
                for ($r = 0; $r < $replyTotal; $r++) {
                    $isStaff = fake()->boolean(60);
                    $msgTs = fake()->dateTimeBetween($msgTs, 'now');
                    SupportTicketMessage::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $isStaff ? fake()->randomElement($admins) : $u->id,
                        'body' => fake()->sentences(fake()->numberBetween(1, 4), true),
                        'is_staff' => $isStaff,
                        'created_at' => $msgTs,
                        'updated_at' => $msgTs,
                    ]);
                    $replyCount++;
                    $lastReplyAt = $msgTs;
                    $lastReplyBy = $isStaff ? 'staff' : 'user';
                }

                $ticket->update([
                    'reply_count' => $replyCount,
                    'last_reply_at' => $lastReplyAt,
                    'last_reply_by' => $lastReplyBy,
                ]);
            }
        }
    }
}
