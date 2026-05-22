<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnlockLinkRequest;
use App\Models\AdImpression;
use App\Models\ShortLink;
use App\Services\AdServingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    public function show(Request $request, string $slug, AdServingService $ads)
    {
        $link = ShortLink::where('slug', $slug)->first();
        abort_if(! $link, 404);

        if (! $link->isActive()) {
            return response()->view('interstitial.blocked', ['link' => $link], 410);
        }

        if ($link->hasPassword() && ! session()->has("unlocked:{$slug}")) {
            return view('interstitial.password', ['slug' => $slug]);
        }

        $picked = $ads->pickForInterstitial();
        $token = (string) Str::uuid();

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

        return view('interstitial.countdown', [
            'link' => $link,
            'ads' => $picked,
            'token' => $token,
            'seconds' => 5,
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function unlock(UnlockLinkRequest $request, string $slug)
    {
        $link = ShortLink::where('slug', $slug)->firstOrFail();
        if (! $link->hasPassword() || ! Hash::check($request->password, $link->password)) {
            return back()->withErrors(['password' => __('Invalid password')]);
        }
        session()->put("unlocked:{$slug}", true);

        return redirect()->route('link.show', $slug);
    }
}
