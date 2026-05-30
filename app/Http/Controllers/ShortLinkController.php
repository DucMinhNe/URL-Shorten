<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortLinkRequest;
use App\Http\Requests\UpdateShortLinkRequest;
use App\Models\ShortLink;
use App\Services\ShortLinkService;
use App\Support\UserAgentParser;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShortLinkController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    public function index(Request $request)
    {
        $query = $request->user()->shortLinks()->with('tags');

        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('slug', 'like', "%{$q}%")
                  ->orWhere('original_url', 'like', "%{$q}%");
            });
        }

        $status = $request->input('status');
        if (in_array($status, ['active', 'disabled', 'blocked'], true)) {
            $query->where('status', $status);
        }

        $tag = trim((string) $request->input('tag', ''));
        if ($tag !== '') {
            $query->whereHas('tags', fn ($w) => $w->where('slug', $tag));
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'clicks' => $query->orderByDesc('total_clicks'),
            'earnings' => $query->orderByDesc('total_earned'),
            default => $query->latest(),
        };

        $links = $query->paginate(20)->withQueryString();

        // Nhãn mà user đang dùng (cho filter bar).
        $userTags = \App\Models\Tag::whereHas('shortLinks', fn ($w) => $w->where('user_id', $request->user()->id))
            ->orderBy('name')->get();

        return view('links.index', compact('links', 'userTags'));
    }

    /** Đồng bộ nhãn từ chuỗi "a, b, c" → tạo/đính Tag, trả về để dùng lại. */
    private function syncTags(ShortLink $link, ?string $raw): void
    {
        if ($raw === null) {
            return;
        }
        $palette = ['violet', 'cyan', 'green', 'amber', 'pink', 'slate'];
        $names = collect(explode(',', $raw))
            ->map(fn ($n) => trim($n))->filter()->unique()->take(8);

        $ids = $names->map(function ($name) use ($palette) {
            $slug = \Illuminate\Support\Str::slug($name) ?: \Illuminate\Support\Str::random(6);
            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'color' => $palette[crc32($slug) % count($palette)]],
            );

            return $tag->id;
        });

        $link->tags()->sync($ids->all());
    }

    public function create()
    {
        return view('links.create');
    }

    public function store(StoreShortLinkRequest $request)
    {
        try {
            $link = $this->svc->create(
                $request->user()->id,
                $request->original_url,
                $request->custom_alias,
                $request->password,
                $request->input('expires_at'),
                $request->filled('max_clicks') ? (int) $request->input('max_clicks') : null,
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['original_url' => __($e->getMessage())])->withInput();
        }

        $this->syncTags($link, $request->input('tags'));

        return redirect()->route('links.index')->with('shortUrl', url('/'.$link->slug));
    }

    public function edit(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);

        return view('links.edit', compact('link'));
    }

    public function update(UpdateShortLinkRequest $request, ShortLink $link)
    {
        try {
            $this->svc->assertOriginalUrlAllowed($request->original_url);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['original_url' => $e->getMessage()])->withInput();
        }

        $data = $request->only(['original_url', 'status']);
        $data['expires_at'] = $request->input('expires_at') ?: null;
        $data['max_clicks'] = $request->filled('max_clicks') ? (int) $request->input('max_clicks') : null;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif ($request->boolean('remove_password')) {
            $data['password'] = null;
        }
        $link->update($data);
        $this->syncTags($link, $request->input('tags'));

        return redirect()->route('links.index')->with('status', __('Đã cập nhật.'));
    }

    public function destroy(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);
        $link->delete();

        return back()->with('success', 'Đã xoá liên kết.');
    }

    /** Bulk action trên nhiều link: bật / tắt / xoá. */
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:activate,disable,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $links = $request->user()->shortLinks()->whereIn('id', $data['ids']);
        $count = (clone $links)->count();

        match ($data['action']) {
            'activate' => $links->update(['status' => 'active']),
            'disable' => $links->update(['status' => 'disabled']),
            'delete' => $links->get()->each->delete(),
        };

        $verb = ['activate' => 'kích hoạt', 'disable' => 'tắt', 'delete' => 'xoá'][$data['action']];

        return back()->with('success', "Đã {$verb} {$count} liên kết.");
    }

    /** Nhân bản 1 link (tạo slug mới, giữ URL + cấu hình). */
    public function clone(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);

        $copy = $this->svc->create(
            $link->user_id,
            $link->original_url,
            null,
            null,
            $link->expires_at?->toDateTimeString(),
            $link->max_clicks,
        );
        $copy->update(['title' => $link->title]);
        $copy->tags()->sync($link->tags()->pluck('tags.id')->all());

        return redirect()->route('links.index')->with('shortUrl', url('/'.$copy->slug));
    }

    /** Export toàn bộ link của user ra CSV (stream, không cần package). */
    public function export(Request $request)
    {
        $user = $request->user();
        $filename = 'linkpay-links-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($user) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM cho Excel đọc UTF-8
            fputcsv($out, ['Slug', 'Short URL', 'URL gốc', 'Trạng thái', 'Click', 'View hợp lệ', 'Doanh thu (đ)', 'Ngày tạo']);
            $user->shortLinks()->orderByDesc('total_clicks')->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $l) {
                    fputcsv($out, [
                        $l->slug, url('/'.$l->slug), $l->original_url, $l->displayStatus(),
                        $l->total_clicks, $l->valid_views, $l->total_earned,
                        $l->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Form rút gọn hàng loạt. */
    public function bulkCreate()
    {
        return view('links.bulk-create');
    }

    /** Rút gọn nhiều URL cùng lúc (mỗi dòng 1 URL). */
    public function bulkStore(Request $request)
    {
        $data = $request->validate(['urls' => 'required|string|max:20000']);

        $lines = collect(preg_split('/\r\n|\r|\n/', $data['urls']))
            ->map(fn ($u) => trim($u))->filter()->unique()->take(50);

        $created = []; $failed = [];
        foreach ($lines as $url) {
            try {
                $link = $this->svc->create($request->user()->id, $url, null, null, null, null);
                $created[] = ['url' => $url, 'short' => url('/'.$link->slug)];
            } catch (\Throwable $e) {
                $failed[] = $url;
            }
        }

        return back()->with('bulkResult', ['created' => $created, 'failed' => $failed]);
    }

    /** Render QR code cho short link (PNG mặc định, ?format=svg, ?download=1). */
    public function qr(Request $request, ShortLink $link)
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $format = $request->query('format') === 'svg' ? 'svg' : 'png';
        $qrCode = new QrCode(data: url('/'.$link->slug), size: 320, margin: 16);
        $writer = $format === 'svg' ? new SvgWriter() : new PngWriter();
        $result = $writer->write($qrCode);

        $disposition = $request->boolean('download')
            ? 'attachment; filename="qr-'.$link->slug.'.'.$format.'"'
            : 'inline';

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Content-Disposition' => $disposition,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    /** Trang thống kê chi tiết cho 1 link — tính từ bảng clicks thật. */
    public function stats(Request $request, ShortLink $link)
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $days = (int) $request->input('days', 30);
        if (! in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        $window = collect(range($days - 1, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'));

        $clicksByDay = $link->clicks()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total, SUM(is_valid) as valid')
            ->groupBy('d')->get()->keyBy('d');

        $labels = $window->map(fn ($d) => substr($d, 5))->values()->toArray();
        $totals = $window->map(fn ($d) => (int) ($clicksByDay[$d]->total ?? 0))->values()->toArray();
        $valids = $window->map(fn ($d) => (int) ($clicksByDay[$d]->valid ?? 0))->values()->toArray();

        // Breakdown từ user_agent / referer (chỉ lấy cột cần, tránh nạp nặng).
        $rows = $link->clicks()
            ->where('created_at', '>=', now()->subDays(max($days, 90)))
            ->get(['user_agent', 'referer']);

        $devices = $browsers = $oses = $referers = [];
        foreach ($rows as $row) {
            $devices[UserAgentParser::deviceType($row->user_agent)] = ($devices[UserAgentParser::deviceType($row->user_agent)] ?? 0) + 1;
            $browsers[UserAgentParser::browser($row->user_agent)] = ($browsers[UserAgentParser::browser($row->user_agent)] ?? 0) + 1;
            $oses[UserAgentParser::os($row->user_agent)] = ($oses[UserAgentParser::os($row->user_agent)] ?? 0) + 1;
            $src = UserAgentParser::refererSource($row->referer);
            $referers[$src] = ($referers[$src] ?? 0) + 1;
        }
        arsort($devices); arsort($browsers); arsort($oses); arsort($referers);
        $topReferers = array_slice($referers, 0, 6, true);

        $validRate = $link->total_clicks > 0
            ? round($link->valid_views / $link->total_clicks * 100, 1)
            : 0;

        return view('links.stats', [
            'link' => $link,
            'labels' => $labels,
            'totals' => $totals,
            'valids' => $valids,
            'devices' => $devices,
            'browsers' => $browsers,
            'oses' => $oses,
            'topReferers' => $topReferers,
            'days' => $days,
            'validRate' => $validRate,
            'windowClicks' => array_sum($totals),
        ]);
    }
}
