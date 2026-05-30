<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnlockLinkRequest;
use App\Models\AdImpression;
use App\Models\ShortLink;
use App\Services\AdServingService;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    public function show(Request $request, string $slug, AdServingService $ads, ClickTrackingService $tracker)
    {
        $link = ShortLink::where('slug', $slug)->first();
        abort_if(! $link, 404);

        if (! $link->isActive()) {
            return response()->view('interstitial.blocked', ['link' => $link, 'reason' => 'blocked'], 410);
        }
        if ($link->isExpired()) {
            return response()->view('interstitial.blocked', ['link' => $link, 'reason' => 'expired'], 410);
        }
        if ($link->isLimitReached()) {
            return response()->view('interstitial.blocked', ['link' => $link, 'reason' => 'limit_reached'], 410);
        }

        if ($link->hasPassword() && ! session()->has("unlocked:{$slug}")) {
            return view('interstitial.password', ['slug' => $slug]);
        }

        // LinkPay Premium: link của chủ Premium bỏ qua trang chờ quảng cáo,
        // redirect thẳng nhưng vẫn ghi nhận click + cộng tiền.
        if ($link->user?->isPremium()) {
            $tracker->record(
                $link, $request->ip(), $request->userAgent(), true,
                $request->user()?->id, $request->headers->get('referer'),
            );

            return redirect()->away($link->original_url);
        }

        $picked = $ads->pickForInterstitial();
        $token = (string) Str::uuid();

        // Bind token to session so /{slug}/verify can detect replay / direct API call.
        session()->put("interstitial:{$token}", [
            'slug' => $slug,
            'issued_at' => now()->timestamp,
        ]);

        foreach ($picked as $ad) {
            if ($ad) {
                AdImpression::create([
                    'ad_campaign_id' => $ad->id,
                    'short_link_id' => $link->id,
                    'impression_token' => $token,
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                ]);
                $ad->increment('impressions');
            }
        }

        $question = \App\Models\CaptchaQuestion::pickActive();
        if ($question) {
            $question->increment('shown_count');
        }

        return view('interstitial.countdown', [
            'link' => $link,
            'ads' => $picked,
            'token' => $token,
            'seconds' => 5,
            'question' => $question,
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function unlock(UnlockLinkRequest $request, string $slug)
    {
        $link = ShortLink::where('slug', $slug)->firstOrFail();
        abort_if(! $link->isActive(), 410);
        if (! $link->hasPassword() || ! Hash::check($request->password, $link->password)) {
            return back()->withErrors(['password' => __('Mật khẩu không đúng.')]);
        }
        session()->put("unlocked:{$slug}", true);

        return redirect()->route('link.show', $slug);
    }
}
