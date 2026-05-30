<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;

class ShortenController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    /** POST /api/v1/shorten — auth bằng header Authorization: Bearer lp_xxx */
    public function store(Request $request)
    {
        $bearer = $request->bearerToken();
        $token = $bearer ? ApiToken::where('token', ApiToken::hash($bearer))->first() : null;

        if (! $token) {
            return response()->json(['error' => 'Token không hợp lệ hoặc thiếu.'], 401);
        }
        $token->forceFill(['last_used_at' => now()])->save();

        $data = $request->validate([
            'url' => 'required|url|max:2048',
            'alias' => 'nullable|string|min:3|max:32',
        ]);

        try {
            $link = $this->svc->create($token->user_id, $data['url'], $data['alias'] ?? null, null, null, null);
        } catch (\Throwable $e) {
            return response()->json(['error' => __($e->getMessage())], 422);
        }

        return response()->json([
            'slug' => $link->slug,
            'short_url' => url('/'.$link->slug),
            'original_url' => $link->original_url,
            'created_at' => $link->created_at,
        ], 201);
    }
}
