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

        $clicksByDay = $base()
            ->where('clicks.created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(clicks.created_at) as d, COUNT(*) as total')
            ->groupBy('d')->pluck('total', 'd');

        $earnedByDay = $base()
            ->where('clicks.created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(clicks.created_at) as d, SUM(earnings) as earned')
            ->groupBy('d')->pluck('earned', 'd');

        $labels = $window->map(fn ($d) => substr($d, 5))->toArray();
        $totals = $window->map(fn ($d) => (int) ($clicksByDay[$d] ?? 0))->toArray();
        $earnings = $window->map(fn ($d) => (int) ($earnedByDay[$d] ?? 0))->toArray();

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

        return view('dashboard', compact('stats', 'labels', 'totals', 'earnings', 'days', 'growthRate'));
    }
}
