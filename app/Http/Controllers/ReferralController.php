<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $code = $user->referralCode();
        $link = url('/register?ref='.$code);

        $referrals = $user->referrals()->latest()->get(['id', 'name', 'email', 'total_earned', 'created_at']);

        $stats = [
            'count' => $referrals->count(),
            'active' => $referrals->where('total_earned', '>', 0)->count(),
            'earned' => $user->referral_earned,
        ];

        return view('referral.index', compact('code', 'link', 'referrals', 'stats'));
    }
}
