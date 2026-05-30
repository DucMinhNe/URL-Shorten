<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function index()
    {
        return view('premium.index');
    }

    /** Demo: nâng cấp Premium 30 ngày (không tính phí thật). */
    public function upgrade(Request $request)
    {
        $plan = $request->input('plan') === 'year' ? 'year' : 'month';
        $user = $request->user();

        // Premium không giới hạn (premium_until = null) → không hạ thành có hạn.
        if ($user->is_premium && $user->premium_until === null) {
            return redirect()->route('premium.index')
                ->with('success', 'Bạn đang là Premium không giới hạn.');
        }

        // Chỉ cộng dồn từ mốc tương lai; nếu đã hết hạn thì tính từ bây giờ.
        $base = ($user->premium_until && $user->premium_until->isFuture()) ? $user->premium_until : now();
        $user->update([
            'is_premium' => true,
            'premium_until' => $plan === 'year' ? $base->copy()->addYear() : $base->copy()->addMonth(),
        ]);

        return redirect()->route('premium.index')
            ->with('success', 'Đã kích hoạt LinkPay Premium · tắt quảng cáo chờ tới '.$user->fresh()->premium_until->format('d/m/Y'));
    }
}
