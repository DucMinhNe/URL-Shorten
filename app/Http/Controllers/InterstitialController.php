<?php

namespace App\Http\Controllers;

use App\Models\AdImpression;
use App\Models\ShortLink;
use App\Services\CaptchaService;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;

class InterstitialController extends Controller
{
    public function __construct(
        private CaptchaService $captcha,
        private ClickTrackingService $tracker,
    ) {}

    public function verify(Request $request, string $slug)
    {
        $data = $request->validate([
            'impression_token' => ['required', 'string', 'size:36'],
            'cf-turnstile-response' => ['nullable', 'string'],
        ]);

        $link = ShortLink::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $captchaPass = $this->captcha->verify(
            $request->input('cf-turnstile-response'),
            $request->ip()
        );

        $click = $this->tracker->record(
            $link,
            $request->ip(),
            $request->userAgent(),
            $captchaPass,
            $request->user()?->id,
            $request->headers->get('referer'),
        );

        AdImpression::where('impression_token', $data['impression_token'])
            ->update(['click_id' => $click->id]);

        return response()->json([
            'redirect_url' => $link->original_url,
            'valid' => $click->is_valid,
        ]);
    }
}
