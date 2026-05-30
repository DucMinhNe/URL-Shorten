<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /** Bảng xếp hạng công khai — top earner (ẩn danh tên). */
    public function index(Request $request)
    {
        $period = $request->input('period') === 'month' ? 'month' : 'all';

        $top = User::query()
            ->where('status', 'active')
            ->where('total_earned', '>', 0)
            ->orderByDesc('total_earned')
            ->limit(20)
            ->get(['id', 'name', 'total_earned', 'created_at'])
            ->map(function ($u, $i) {
                $parts = preg_split('/\s+/', trim($u->name));
                $masked = $parts[0].' '.(isset($parts[1]) ? mb_substr(end($parts), 0, 1).'.' : '');

                return (object) [
                    'rank' => $i + 1,
                    'name' => trim($masked),
                    'earned' => $u->total_earned,
                    'since' => $u->created_at,
                ];
            });

        $totalPaid = (int) User::sum('total_earned');
        $members = User::count();

        return view('leaderboard', compact('top', 'period', 'totalPaid', 'members'));
    }
}
