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
        $links = $request->user()->shortLinks()->latest()->paginate(20);

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
        $data = $request->only(['original_url', 'status']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif ($request->boolean('remove_password')) {
            $data['password'] = null;
        }
        $link->update($data);

        return redirect()->route('links.index')->with('status', __('Updated'));
    }

    public function destroy(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);
        $link->delete();

        return back()->with('status', __('Deleted'));
    }
}
