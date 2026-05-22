<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ __('Loading link...') }}</title>
@vite('resources/css/app.css')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex flex-col">

<header class="bg-white dark:bg-gray-800 shadow py-3 px-4 text-center text-sm">
    <span class="font-semibold">{{ config('app.name') }}</span> — {{ __('Please wait, your link is loading') }}
</header>

@if($ads['top'])
<div class="w-full bg-white dark:bg-gray-800 py-3 flex justify-center border-b">
    @include('interstitial._ad-slot', ['ad' => $ads['top']])
</div>
@endif

<main class="flex-1 flex">
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full text-center">
            <p class="text-gray-500 mb-2 text-sm">{{ __('Your destination is loading...') }}</p>
            <div class="text-6xl font-bold text-blue-600 my-4" x-data="{c:{{ $seconds }}, captchaOk:false}" x-init="
                let i=setInterval(()=>{c--; if(c<=0){clearInterval(i); $refs.btn.disabled=!captchaOk;}},1000);
                window.__captchaOk = () => {captchaOk=true; if(c<=0) $refs.btn.disabled=false;};
            " x-text="c"></div>

            <div class="cf-turnstile my-4" data-sitekey="{{ $turnstileSiteKey }}" data-callback="__captchaOk"></div>

            <form id="verify-form" method="POST" action="{{ route('link.verify', $link->slug) }}">
                @csrf
                <input type="hidden" name="impression_token" value="{{ $token }}">
                <button x-ref="btn" type="button" disabled
                    @click="
                        let fd = new FormData(document.getElementById('verify-form'));
                        let tk = document.querySelector('[name=cf-turnstile-response]');
                        if(tk) fd.append('cf-turnstile-response', tk.value);
                        fetch('{{ route('link.verify', $link->slug) }}', {method:'POST', body: fd, headers:{'X-Requested-With':'XMLHttpRequest'}})
                          .then(r=>r.json()).then(d => window.location.href = d.redirect_url);
                    "
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 rounded transition">
                    {{ __('Skip Ad') }}
                </button>
            </form>
        </div>
    </div>

    @if($ads['side'])
    <aside class="hidden lg:flex w-[320px] items-center justify-center p-4 border-l">
        @include('interstitial._ad-slot', ['ad' => $ads['side']])
    </aside>
    @endif
</main>

@if($ads['bottom'])
<div class="w-full bg-white dark:bg-gray-800 py-3 flex justify-center border-t">
    @include('interstitial._ad-slot', ['ad' => $ads['bottom']])
</div>
@endif

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
