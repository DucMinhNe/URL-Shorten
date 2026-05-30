<?php

namespace App\Http\Controllers;

use App\Models\Click;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $days = (int) $request->input('days', 30);
        if (! in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        $window = collect(range($days - 1, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'));

        $base = fn () => Click::join('short_links', 'clicks.short_link_id', '=', 'short_links.id')
            ->where('short_links.user_id', $user->id);

        $byDay = $base()
            ->where('clicks.created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(clicks.created_at) as d, COUNT(*) as total, SUM(is_valid) as valid, SUM(earnings) as earned')
            ->groupBy('d')->get()->keyBy('d');

        $labels = $window->map(fn ($d) => substr($d, 5))->toArray();
        $totals = $window->map(fn ($d) => (int) ($byDay[$d]->total ?? 0))->toArray();
        $valids = $window->map(fn ($d) => (int) ($byDay[$d]->valid ?? 0))->toArray();
        $earnings = $window->map(fn ($d) => (int) ($byDay[$d]->earned ?? 0))->toArray();

        // Sparkline ngắn (14 ngày cuối) cho các thẻ KPI.
        $sparkClicks = array_slice($totals, -14);
        $sparkValids = array_slice($valids, -14);
        $sparkEarnings = array_slice($earnings, -14);

        // Thu nhập tháng này vs tháng trước (cho thẻ + delta).
        $earnedThisMonth = (int) $base()
            ->where('clicks.created_at', '>=', now()->startOfMonth())->sum('earnings');
        $earnedLastMonth = (int) $base()
            ->whereBetween('clicks.created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->startOfMonth()])
            ->sum('earnings');
        $monthDelta = $earnedLastMonth > 0
            ? round(($earnedThisMonth - $earnedLastMonth) / $earnedLastMonth * 100, 1)
            : null;

        // Growth rate vs previous equivalent window.
        $currentClicks = array_sum($totals);
        $previousClicks = (int) $base()
            ->whereBetween('clicks.created_at', [now()->subDays($days * 2), now()->subDays($days)])
            ->count();
        $growthRate = $previousClicks > 0
            ? round(($currentClicks - $previousClicks) / $previousClicks * 100, 1)
            : null;

        $stats = [
            'total_links' => $user->shortLinks()->count(),
            'total_clicks' => $user->shortLinks()->sum('total_clicks'),
            'valid_views' => $user->shortLinks()->sum('valid_views'),
            'balance' => $user->balance,
            'total_earned' => $user->total_earned,
        ];

        // Hoạt động gần đây — click mới nhất trên các link của user (data thật).
        $recentClicks = Click::join('short_links', 'clicks.short_link_id', '=', 'short_links.id')
            ->where('short_links.user_id', $user->id)
            ->orderByDesc('clicks.created_at')
            ->limit(6)
            ->get([
                'short_links.slug as slug',
                'clicks.user_agent as user_agent',
                'clicks.referer as referer',
                'clicks.is_valid as is_valid',
                'clicks.earnings as earnings',
                'clicks.created_at as created_at',
            ]);

        return view('dashboard', compact(
            'stats', 'labels', 'totals', 'valids', 'earnings', 'days', 'growthRate', 'recentClicks',
            'sparkClicks', 'sparkValids', 'sparkEarnings', 'earnedThisMonth', 'earnedLastMonth', 'monthDelta',
        ));
    }
}
