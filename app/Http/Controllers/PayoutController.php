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
        $requests = $request->user()->payoutRequests()->latest()->paginate(15);
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
            return back()->withErrors(['amount' => __($e->getMessage())])->withInput();
        }
        return redirect()->route('payout.index')->with('status', __('Payout request submitted'));
    }
}
