<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đang tải liên kết... · LinkPay</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-surface-soft min-h-screen flex flex-col">

{{-- Top bar --}}
<header class="bg-canvas border-b border-hairline-soft">
    <div class="max-w-[1400px] mx-auto px-6 h-14 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <svg width="22" height="22" viewBox="0 0 32 32" fill="none">
                <rect x="2" y="2" width="28" height="28" rx="8" fill="#0A1317"/>
                <path d="M11 18.5 L21 13.5" stroke="#0064E0" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="11" cy="18.5" r="3" fill="#0064E0"/>
                <circle cx="21" cy="13.5" r="3" fill="#0064E0"/>
            </svg>
            <span class="type-link-md">LinkPay</span>
        </a>
        <div class="flex items-center gap-2 type-body-sm text-slate">
            <span class="w-2 h-2 rounded-full bg-success pulse-dot"></span>
            <span class="hidden sm:inline">Đang tải liên kết của bạn...</span>
        </div>
        <div class="type-caption-bold uppercase tracking-wider text-stone hidden md:flex items-center gap-1">
            <x-heroicon-o-information-circle class="w-3.5 h-3.5"/>
            Quảng cáo · Sponsored
        </div>
    </div>
</header>

{{-- Top ad slot --}}
@if($ads['top'])
    <div class="bg-canvas border-b border-hairline-soft">
        <div class="max-w-[1400px] mx-auto px-6 py-3 flex justify-center">
            <div class="rounded-2xl overflow-hidden max-w-[728px] w-full" style="aspect-ratio: 728/90;">
                @include('interstitial._ad-slot', ['ad' => $ads['top']])
            </div>
        </div>
    </div>
@endif

{{-- Main --}}
<main class="flex-1 flex">
    <div class="flex-1 flex items-center justify-center p-6">
        <div class="card-feature !p-10 max-w-[480px] w-full text-center relative" id="card">

            <div class="section-label justify-center mb-3"><span>Đang chuẩn bị liên kết</span></div>

            <p class="type-body-sm text-slate">Bạn sẽ được chuyển trong</p>

            <div class="my-6 relative inline-block">
                {{-- Circular progress --}}
                <svg width="180" height="180" viewBox="0 0 180 180" class="transform -rotate-90">
                    <circle cx="90" cy="90" r="78" fill="none" stroke="rgba(10,19,23,0.08)" stroke-width="8"/>
                    <circle id="progress-ring" cx="90" cy="90" r="78" fill="none" stroke="#0064E0" stroke-width="8" stroke-linecap="round"
                            stroke-dasharray="490" stroke-dashoffset="0" style="transition: stroke-dashoffset 1s linear"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div id="countdown" class="font-bold text-ink-deep" style="font-size: 88px; line-height: 1; letter-spacing: -0.04em;">{{ $seconds }}</div>
                    <div class="type-caption-bold uppercase tracking-wider text-stone mt-1">giây</div>
                </div>
            </div>

            {{-- Captcha --}}
            <div class="my-6">
                <div class="cf-turnstile mx-auto" data-sitekey="{{ $turnstileSiteKey }}" data-callback="onCaptchaPass"></div>
            </div>

            <form id="verify-form" method="POST" action="{{ route('link.verify', $link->slug) }}">
                @csrf
                <input type="hidden" name="impression_token" value="{{ $token }}">
                <button id="skip-btn" type="button" disabled class="btn btn-primary w-full">
                    <span id="skip-label">Đợi {{ $seconds }} giây...</span>
                    <x-heroicon-m-arrow-right class="w-4 h-4"/>
                </button>
            </form>

            <p class="type-caption text-stone mt-5">
                Bằng việc tiếp tục, bạn đồng ý <a href="#" class="text-ink-deep font-bold underline">điều khoản</a> của LinkPay
            </p>
        </div>
    </div>

    {{-- Side ad --}}
    @if($ads['side'])
        <aside class="hidden lg:flex w-[340px] items-center justify-center p-6 border-l border-hairline-soft bg-canvas">
            <div class="space-y-3 w-full">
                <div class="rounded-2xl overflow-hidden" style="aspect-ratio: 300/250; max-width: 300px;">
                    @include('interstitial._ad-slot', ['ad' => $ads['side']])
                </div>
                <button class="type-caption text-stone hover:text-ink-deep w-full text-center">
                    Báo cáo quảng cáo này
                </button>
            </div>
        </aside>
    @endif
</main>

{{-- Bottom ad --}}
@if($ads['bottom'])
    <div class="bg-canvas border-t border-hairline-soft">
        <div class="max-w-[1400px] mx-auto px-6 py-3 flex justify-center">
            <div class="rounded-2xl overflow-hidden max-w-[728px] w-full" style="aspect-ratio: 728/90;">
                @include('interstitial._ad-slot', ['ad' => $ads['bottom']])
            </div>
        </div>
    </div>
@endif

{{-- Footer --}}
<footer class="bg-canvas border-t border-hairline-soft">
    <div class="max-w-[1400px] mx-auto px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2 type-caption text-stone">
        <span>LinkPay · Quảng cáo giúp creator kiếm tiền. Cảm ơn 5 giây của bạn ❤️</span>
        <a href="#" class="font-bold text-ink-deep hover:text-primary">Tắt quảng cáo Pro: 49.000đ/tháng →</a>
    </div>
</footer>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
(() => {
    const TOTAL = {{ $seconds }};
    const ring = document.getElementById('progress-ring');
    const cd = document.getElementById('countdown');
    const btn = document.getElementById('skip-btn');
    const lbl = document.getElementById('skip-label');
    const card = document.getElementById('card');
    const FULL = 2 * Math.PI * 78;
    let c = TOTAL;
    let captchaOk = false;

    ring.setAttribute('stroke-dasharray', FULL);
    ring.style.strokeDashoffset = 0;

    function refresh() {
        if (c > 0 && !captchaOk) lbl.textContent = `Đợi ${c} giây...`;
        else if (c > 0 && captchaOk) lbl.textContent = `Đợi ${c} giây...`;
        else if (c <= 0 && !captchaOk) lbl.textContent = 'Hoàn tất captcha để tiếp tục';
        else lbl.textContent = 'Bỏ qua quảng cáo';
        btn.disabled = !(c <= 0 && captchaOk);
    }

    const interval = setInterval(() => {
        c--;
        cd.textContent = Math.max(0, c);
        ring.style.strokeDashoffset = (FULL * (TOTAL - c)) / TOTAL;
        refresh();
        if (c <= 0) clearInterval(interval);
    }, 1000);

    window.onCaptchaPass = () => { captchaOk = true; refresh(); };

    btn.addEventListener('click', async () => {
        if (btn.disabled) return;
        btn.disabled = true;
        lbl.textContent = 'Đang chuyển hướng...';
        card.style.opacity = '0.6';

        const fd = new FormData(document.getElementById('verify-form'));
        const turn = document.querySelector('[name="cf-turnstile-response"]');
        if (turn) fd.append('cf-turnstile-response', turn.value);

        try {
            const r = await fetch('{{ route('link.verify', $link->slug) }}', {
                method: 'POST', body: fd, headers: {'X-Requested-With':'XMLHttpRequest'}
            });
            const d = await r.json();
            window.location.href = d.redirect_url;
        } catch(e) {
            lbl.textContent = 'Lỗi kết nối, thử lại';
            btn.disabled = false;
            card.style.opacity = '1';
        }
    });

    refresh();
})();
</script>
</body>
</html>
