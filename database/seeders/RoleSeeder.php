<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin', 'slug' => 'super-admin', 'level' => 100, 'color' => 'rose',
                'description' => 'Toàn quyền — không thể xoá', 'is_system' => true,
                'permissions' => ['*'],
            ],
            [
                'name' => 'Admin', 'slug' => 'admin', 'level' => 80, 'color' => 'violet',
                'description' => 'Quản trị viên — duyệt rút tiền, ban user, mod link',
                'permissions' => [
                    'users.view', 'users.edit', 'users.ban',
                    'payouts.view', 'payouts.approve', 'payouts.reject',
                    'links.view', 'links.disable',
                    'reports.review', 'tickets.respond',
                    'announcements.manage', 'promos.manage',
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Moderator', 'slug' => 'moderator', 'level' => 60, 'color' => 'blue',
                'description' => 'Duyệt báo cáo + ticket support, không động vào tiền',
                'permissions' => [
                    'reports.review', 'tickets.respond', 'links.disable', 'users.view',
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Creator', 'slug' => 'creator', 'level' => 20, 'color' => 'emerald',
                'description' => 'User chính — tạo link, rút tiền',
                'permissions' => ['links.create', 'links.edit_own', 'payouts.request'],
                'is_system' => true,
            ],
            [
                'name' => 'Viewer', 'slug' => 'viewer', 'level' => 0, 'color' => 'gray',
                'description' => 'Tài khoản hạn chế — chỉ xem stats, không tạo link',
                'permissions' => ['stats.view'],
                'is_system' => true,
            ],
            [
                'name' => 'VIP Creator', 'slug' => 'vip-creator', 'level' => 30, 'color' => 'amber',
                'description' => 'Creator có rate boost 1.5x, ưu tiên duyệt rút trong 6h',
                'permissions' => ['links.create', 'links.edit_own', 'payouts.request', 'payouts.priority'],
                'is_system' => false,
            ],
        ];

        foreach ($roles as $r) {
            Role::create($r);
        }

        // Assign roles to existing users
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $creator = Role::where('slug', 'creator')->first();
        $vip = Role::where('slug', 'vip-creator')->first();
        $viewer = Role::where('slug', 'viewer')->first();

        // Admin user → super-admin role
        User::where('email', 'admin@demo.com')->update(['role_id' => $superAdmin->id]);
        // Demo user → creator
        User::where('email', 'demo@demo.com')->update(['role_id' => $creator->id]);

        // Distribute roles among the other users
        $otherIds = User::where('is_admin', false)->where('id', '!=', User::where('email', 'demo@demo.com')->value('id'))->pluck('id');
        $vipCount = (int) ($otherIds->count() * 0.08);  // 8% VIPs
        $viewerCount = (int) ($otherIds->count() * 0.05);  // 5% viewers
        $shuffled = $otherIds->shuffle();

        DB::table('users')->whereIn('id', $shuffled->slice(0, $vipCount))->update(['role_id' => $vip->id]);
        DB::table('users')->whereIn('id', $shuffled->slice($vipCount, $viewerCount))->update(['role_id' => $viewer->id]);
        DB::table('users')->whereIn('id', $shuffled->slice($vipCount + $viewerCount))->update(['role_id' => $creator->id]);
    }
}
