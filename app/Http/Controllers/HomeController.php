<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Services\SettingService;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    public function index(SettingService $settings)
    {
        return view('home', [
            'stats' => $this->landingStats($settings),
            'faqs' => $this->featuredFaqs(),
        ]);
    }

    public function faq()
    {
        $groups = config('faq.groups', []);
        $flat = [];
        foreach ($groups as $group) {
            foreach ($group['items'] ?? [] as $item) {
                $flat[] = ['q' => $item['q'], 'a' => $item['a']];
            }
        }

        return view('faq', [
            'groups' => $groups,
            'faqsFlat' => $flat,
        ]);
    }

    public function shortenGuest(Request $request)
    {
        $data = Validator::make($request->all(), [
            'original_url' => ['required', 'url:http,https', 'max:2048'],
            'custom_alias' => ['nullable', 'alpha_dash', 'min:3', 'max:32', 'unique:short_links,slug'],
        ])->validate();

        try {
            $link = $this->svc->create(null, $data['original_url'], $data['custom_alias'] ?? null);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['original_url' => __($e->getMessage())])->withInput();
        }

        return back()->with('shortUrl', url('/'.$link->slug));
    }

    /** Số liệu thật cho stats band ở landing (có fallback khi DB trống). */
    private function landingStats(SettingService $settings): array
    {
        $rate = (int) $settings->get('rate_per_1000_views', 5000);
        $validViews = (int) ShortLink::sum('valid_views');
        $activeLinks = (int) ShortLink::where('status', 'active')->count();

        return [
            'valid_views' => $validViews,
            'rate_per_1000' => $rate,
            'active_links' => $activeLinks,
            'payout_methods' => 3, // MoMo · ZaloPay · PayPal
        ];
    }

    /** Lấy các câu hỏi đánh dấu featured từ config (không DB). */
    private function featuredFaqs(): array
    {
        $featured = [];
        foreach (config('faq.groups', []) as $group) {
            foreach ($group['items'] ?? [] as $item) {
                if ($item['featured'] ?? false) {
                    $featured[] = ['q' => $item['q'], 'a' => $item['a']];
                }
            }
        }

        return $featured;
    }
}
