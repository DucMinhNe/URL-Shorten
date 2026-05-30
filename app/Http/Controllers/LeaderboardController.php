<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /** Bảng xếp hạng công khai — top earner (ẩn danh tên). */
    public function index(Request $request)
    {
        $period = $request->input('period') === 'month' ? 'month' : 'all';

        $mask = function ($name) {
            $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
            return trim(($parts[0] ?? 'Ẩn').' '.(isset($parts[1]) ? mb_substr(end($parts), 0, 1).'.' : ''));
        };

        if ($period === 'month') {
            // Xếp hạng theo thu nhập THÁNG NÀY (tổng earnings click trong tháng).
            $rows = Click::query()
                ->join('short_links', 'clicks.short_link_id', '=', 'short_links.id')
                ->join('users', 'short_links.user_id', '=', 'users.id')
                ->where('users.status', 'active')
                ->where('clicks.created_at', '>=', now()->startOfMonth())
                ->groupBy('users.id', 'users.name', 'users.created_at')
                ->havingRaw('SUM(clicks.earnings) > 0')
                ->orderByRaw('SUM(clicks.earnings) DESC')
                ->limit(20)
                ->get([
                    'users.id', 'users.name', 'users.created_at as since',
                    DB::raw('SUM(clicks.earnings) as earned'),
                ]);
        } else {
            $rows = User::query()
                ->where('status', 'active')
                ->where('total_earned', '>', 0)
                ->orderByDesc('total_earned')
                ->limit(20)
                ->get(['id', 'name', 'total_earned as earned', 'created_at as since']);
        }

        $top = $rows->values()->map(fn ($u, $i) => (object) [
            'rank' => $i + 1,
            'name' => $mask($u->name),
            'earned' => (int) $u->earned,
            'since' => Carbon::parse($u->since),
        ]);

        // Totals khớp tập xếp hạng (active earner), không tính user banned / 0đ.
        $earnerQuery = User::where('status', 'active')->where('total_earned', '>', 0);
        $totalPaid = (int) (clone $earnerQuery)->sum('total_earned');
        $members = (clone $earnerQuery)->count();

        return view('leaderboard', compact('top', 'period', 'totalPaid', 'members'));
    }
}
