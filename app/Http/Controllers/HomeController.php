<?php

namespace App\Http\Controllers;

use App\Services\ShortLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    public function index()
    {
        return view('home');
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
}
