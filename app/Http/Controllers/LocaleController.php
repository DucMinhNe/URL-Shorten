<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        abort_unless(in_array($locale, ['vi', 'en']), 404);

        if ($user = $request->user()) {
            $user->update(['preferred_locale' => $locale]);
        }

        return back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
