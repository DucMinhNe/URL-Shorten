<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index(Request $request)
    {
        $tokens = $request->user()->apiTokens()->latest()->get();

        return view('api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:60']);

        $plain = ApiToken::generate();
        $request->user()->apiTokens()->create([
            'name' => $data['name'],
            'token' => $plain,
        ]);

        // Hiện token đầy đủ đúng một lần qua flash.
        return back()->with('newToken', $plain)->with('success', 'Đã tạo API token.');
    }

    public function destroy(Request $request, ApiToken $token)
    {
        abort_unless($token->user_id === $request->user()->id, 403);
        $token->delete();

        return back()->with('success', 'Đã thu hồi token.');
    }
}
