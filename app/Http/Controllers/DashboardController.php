<?php

namespace App\Http\Controllers;

use App\Models\Click;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $days = collect(range(29, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'));

        $clicksByDay = Click::join('short_links', 'clicks.short_link_id', '=', 'short_links.id')
            ->where('short_links.user_id', $user->id)
            ->where('clicks.created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(clicks.created_at) as d, COUNT(*) as total')
            ->groupBy('d')->pluck('total', 'd');

        $earnedByDay = Click::join('short_links', 'clicks.short_link_id', '=', 'short_links.id')
            ->where('short_links.user_id', $user->id)
            ->where('clicks.created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(clicks.created_at) as d, SUM(earnings) as earned')
            ->groupBy('d')->pluck('earned', 'd');

        $labels = $days->map(fn ($d) => substr($d, 5))->toArray();
        $totals = $days->map(fn ($d) => (int) ($clicksByDay[$d] ?? 0))->toArray();
        $earnings = $days->map(fn ($d) => (int) ($earnedByDay[$d] ?? 0))->toArray();

        $stats = [
            'total_links' => $user->shortLinks()->count(),
            'total_clicks' => $user->shortLinks()->sum('total_clicks'),
            'valid_views' => $user->shortLinks()->sum('valid_views'),
            'balance' => $user->balance,
            'total_earned' => $user->total_earned,
        ];

        return view('dashboard', compact('stats', 'labels', 'totals', 'earnings'));
    }
}
