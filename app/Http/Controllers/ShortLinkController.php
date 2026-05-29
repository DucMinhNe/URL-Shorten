<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortLinkRequest;
use App\Http\Requests\UpdateShortLinkRequest;
use App\Models\ShortLink;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShortLinkController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    public function index(Request $request)
    {
        $query = $request->user()->shortLinks();

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

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'clicks' => $query->orderByDesc('total_clicks'),
            'earnings' => $query->orderByDesc('total_earned'),
            default => $query->latest(),
        };

        $links = $query->paginate(20)->withQueryString();

        return view('links.index', compact('links'));
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
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['original_url' => __($e->getMessage())])->withInput();
        }

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
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif ($request->boolean('remove_password')) {
            $data['password'] = null;
        }
        $link->update($data);

        return redirect()->route('links.index')->with('status', __('Đã cập nhật.'));
    }

    public function destroy(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);
        $link->delete();

        return back()->with('status', __('Deleted'));
    }
}
