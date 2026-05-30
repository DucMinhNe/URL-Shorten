<?php

namespace App\Http\Controllers;

use App\Models\AdCampaign;
use App\Models\AdImpression;
use App\Models\ShortLink;
use App\Services\CaptchaService;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;

class InterstitialController extends Controller
{
    /** Track click quảng cáo rồi chuyển tới trang đích của campaign. */
    public function adClick(AdCampaign $campaign, string $token)
    {
        $updated = AdImpression::where('impression_token', $token)
            ->where('ad_campaign_id', $campaign->id)
            ->where('was_clicked', false)
            ->update(['was_clicked' => true]);

        if ($updated) {
            $campaign->increment('clicks_count');
        }

        return redirect()->away($campaign->target_url ?: route('home'));
    }

    private const MIN_DWELL_SECONDS = 4;

    public function __construct(
        private CaptchaService $captcha,
        private ClickTrackingService $tracker,
    ) {}

    public function verify(Request $request, string $slug)
    {
        $data = $request->validate([
            'impression_token' => ['required', 'string', 'size:36'],
            'cf-turnstile-response' => ['nullable', 'string'],
            'captcha_question_id' => ['nullable', 'integer'],
            'captcha_answer' => ['nullable', 'string'],
        ]);

        // Nếu trang chờ hiển thị câu hỏi → bắt buộc trả lời đúng.
        if (! empty($data['captcha_question_id'])) {
            $question = \App\Models\CaptchaQuestion::find($data['captcha_question_id']);
            if (! $question || ! $question->isSolved($data['captcha_answer'] ?? null)) {
                return response()->json([
                    'error' => 'captcha_failed',
                    'message' => 'Vui lòng trả lời đúng câu hỏi xác minh.',
                ], 422);
            }
        }

        $sessionKey = "interstitial:{$data['impression_token']}";
        $meta = session()->pull($sessionKey); // one-time use — pull removes from session
        if (! $meta || ($meta['slug'] ?? null) !== $slug) {
            return response()->json([
                'error' => 'invalid_token',
                'message' => 'Token không hợp lệ hoặc đã sử dụng.',
            ], 403);
        }

        $elapsed = now()->timestamp - ($meta['issued_at'] ?? 0);
        if ($elapsed < self::MIN_DWELL_SECONDS) {
            return response()->json([
                'error' => 'too_fast',
                'message' => 'Vui lòng chờ đủ thời gian xem quảng cáo.',
            ], 422);
        }

        $link = ShortLink::where('slug', $slug)->where('status', 'active')->first();
        if (! $link) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $captchaPass = $this->captcha->verify(
            $request->input('cf-turnstile-response'),
            $request->ip()
        );

        if (! $captchaPass) {
            return response()->json([
                'error' => 'captcha_failed',
                'message' => 'Vui lòng xác nhận bạn không phải robot.',
            ], 422);
        }

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
