<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayoutRequest;
use App\Services\PayoutService;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function __construct(private PayoutService $svc) {}

    public function index(Request $request)
    {
        $query = $request->user()->payoutRequests();

        $status = $request->input('status');
        if (in_array($status, ['pending', 'approved', 'paid', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('payout.index', compact('requests'));
    }

    public function store(StorePayoutRequest $request)
    {
        try {
            $this->svc->createRequest(
                $request->user(),
                (int) $request->amount,
                $request->method,
                $request->account_info,
            );
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
        return redirect()->route('payout.index')->with('status', 'Đã gửi yêu cầu rút tiền. Admin sẽ duyệt trong 24h.');
    }
}
