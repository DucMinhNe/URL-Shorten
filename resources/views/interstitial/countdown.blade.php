<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đang chuyển hướng... · LinkPay</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
@vite('resources/css/app.css')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<style>
    body { font-family: 'Public Sans', system-ui, sans-serif; }
    .gradient-mask { mask-image: linear-gradient(180deg, transparent 0%, black 8%, black 92%, transparent 100%); }

    /* Skip button: starts disabled gray, animates to bright yellow when ready */
    .skip-btn {
        background: #E5E7EB;
        color: #9CA3AF;
        cursor: not-allowed;
        box-shadow: none;
        transition: all .4s cubic-bezier(.16,1,.3,1);
    }
    .skip-btn.ready {
        background: linear-gradient(135deg, #FFD60A 0%, #FFAB00 100%);
        color: #0A1317;
        cursor: pointer;
        box-shadow: 0 6px 20px -4px rgba(255, 171, 0, .55), 0 0 0 1px rgba(255, 171, 0, .35);
    }
    .skip-btn.ready:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px -4px rgba(255, 171, 0, .65), 0 0 0 1px rgba(255, 171, 0, .45);
    }

    /* Countdown ring */
    .ring-bg { stroke: rgba(105, 108, 255, .15); }
    .ring-fg { stroke: #696CFF; transition: stroke-dashoffset 1s linear; }

    /* Ad slot tag chip */
    .ad-tag {
        position: absolute; top: 12px; left: 12px;
        background: rgba(10,19,23,.7); color: #fff;
        font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        padding: 4px 8px; border-radius: 6px; backdrop-filter: blur(8px);
        z-index: 2;
    }

    /* Pulse for countdown number */
    @keyframes tick {
        0%, 90% { transform: scale(1); }
        95% { transform: scale(1.08); }
    }
    #countdown.ticking { animation: tick 1s ease-out infinite; }

    /* Ready celebration */
    @keyframes ready-bounce {
        0% { transform: scale(.8); opacity: 0; }
        60% { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }
    .ready-bounce { animation: ready-bounce .5s cubic-bezier(.16,1.4,.3,1); }
</style>
</head>
<body class="bg-[#F5F5F9] min-h-screen flex flex-col">

{{-- ═══════════════════════════════════════════════════════════
     STICKY HEADER BAR — adf.ly-style with countdown + Skip CTA
     ═══════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-50 bg-white border-b border-[#E4E6E8]" style="box-shadow: 0 2px 6px 0 rgba(34,48,62,.05);">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-6 h-16 flex items-center gap-3 lg:gap-6">

        {{-- Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                <rect x="2" y="2" width="28" height="28" rx="8" fill="#0A1317"/>
                <path d="M11 18.5 L21 13.5" stroke="#696CFF" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="11" cy="18.5" r="3" fill="#696CFF"/>
                <circle cx="21" cy="13.5" r="3" fill="#696CFF"/>
            </svg>
            <span class="font-bold text-[#384551] text-base">LinkPay</span>
        </a>

        {{-- Destination indicator (hidden on mobile) --}}
        <div class="hidden md:flex flex-1 items-center gap-2 min-w-0">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#F5F5F9] rounded-full text-sm min-w-0">
                <svg class="w-3.5 h-3.5 text-[#71DD37] flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/>
                </svg>
                <span class="text-[#8592A3] text-xs font-medium uppercase tracking-wider hidden lg:inline">Sắp đến</span>
                <span class="text-[#384551] font-mono text-sm truncate max-w-[280px] xl:max-w-[420px]" id="destination-host">{{ parse_url($link->original_url, PHP_URL_HOST) }}</span>
            </div>
        </div>

        {{-- Right cluster: captcha + countdown + skip --}}
        <div class="ml-auto flex items-center gap-3 lg:gap-5 flex-shrink-0">

            {{-- Mini countdown ring --}}
            <div class="hidden sm:flex items-center gap-2">
                <div class="relative w-10 h-10">
                    <svg class="w-10 h-10 transform -rotate-90" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="16" fill="none" class="ring-bg" stroke-width="3"/>
                        <circle id="ring-mini" cx="20" cy="20" r="16" fill="none" class="ring-fg" stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="100.5" stroke-dashoffset="0"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="countdown" class="text-[#384551] font-bold text-sm ticking">{{ $seconds }}</span>
                    </div>
                </div>
                <span class="hidden lg:inline text-xs text-[#8592A3] font-medium uppercase tracking-wider">giây</span>
            </div>

            {{-- Captcha (compact) --}}
            <div id="captcha-wrap" class="hidden lg:block">
                <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-callback="onCaptchaPass" data-size="compact"></div>
            </div>

            {{-- The big yellow Skip Ad button (adf.ly signature) --}}
            <form id="verify-form" method="POST" action="{{ route('link.verify', $link->slug) }}">
                @csrf
                <input type="hidden" name="impression_token" value="{{ $token }}">
                <button id="skip-btn" type="button" disabled
                        class="skip-btn flex items-center gap-2 font-bold text-sm px-5 py-2.5 rounded-full whitespace-nowrap">
                    <span id="skip-label">Đợi {{ $seconds }} giây</span>
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3.105 2.288a.75.75 0 00-.826.95l1.414 4.926A.75.75 0 004.42 8.75H10a.75.75 0 010 1.5H4.42a.75.75 0 00-.726.554l-1.414 4.926a.75.75 0 00.826.95 28.897 28.897 0 0015.293-7.155.75.75 0 000-1.05A28.897 28.897 0 003.105 2.288z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Mobile destination row --}}
    <div class="md:hidden px-4 pb-2 -mt-1 flex items-center justify-between gap-2 text-xs">
        <span class="text-[#8592A3]">Sắp đến:</span>
        <span class="text-[#384551] font-mono truncate flex-1 text-right">{{ parse_url($link->original_url, PHP_URL_HOST) }}</span>
    </div>

    {{-- Captcha mobile/tablet placement --}}
    <div class="lg:hidden border-t border-[#E4E6E8] py-2.5 flex justify-center">
        <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-callback="onCaptchaPass" data-size="compact"></div>
    </div>
</header>

{{-- ═══════════════════════════════════════════════════════════
     MAIN — Featured top ad + 2-up bottom ads
     ═══════════════════════════════════════════════════════════ --}}
<main class="flex-1 py-6 lg:py-10">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-6 space-y-6">

        {{-- Hint banner: instructions --}}
        <div class="bg-gradient-to-r from-[#E7E7FF] to-[#F0F0FF] border border-[#D2D3FF] rounded-2xl px-5 py-3.5 flex items-center gap-3 text-sm">
            <div class="w-8 h-8 rounded-full bg-[#696CFF] text-white flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-[#384551]">Liên kết của bạn đang được tải</div>
                <div class="text-[#646E78] text-xs mt-0.5">Vui lòng chờ <span id="hint-seconds" class="font-bold text-[#696CFF]">{{ $seconds }}</span> giây và hoàn thành xác thực — sau đó nhấn nút <span class="font-bold text-[#0A1317]">Bỏ qua quảng cáo</span> ở góc trên bên phải.</div>
            </div>
        </div>

        {{-- TOP FEATURED AD: full width, big hero --}}
        @if($ads['top'])
            <div class="relative">
                <span class="ad-tag">Quảng cáo</span>
                <div class="bg-white border border-[#E4E6E8] rounded-3xl overflow-hidden" style="box-shadow: 0 4px 12px 0 rgba(34,48,62,.08);">
                    <div class="relative" style="aspect-ratio: 21/6;">
                        @include('interstitial._ad-slot', ['ad' => $ads['top']])
                    </div>
                </div>
            </div>
        @endif

        {{-- BOTTOM TWO-UP --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
            @if($ads['side'])
                <div class="relative">
                    <span class="ad-tag">Quảng cáo</span>
                    <div class="bg-white border border-[#E4E6E8] rounded-3xl overflow-hidden" style="box-shadow: 0 4px 12px 0 rgba(34,48,62,.08);">
                        <div class="relative" style="aspect-ratio: 4/3;">
                            @include('interstitial._ad-slot', ['ad' => $ads['side']])
                        </div>
                    </div>
                </div>
            @endif
            @if($ads['bottom'])
                <div class="relative">
                    <span class="ad-tag">Quảng cáo</span>
                    <div class="bg-white border border-[#E4E6E8] rounded-3xl overflow-hidden" style="box-shadow: 0 4px 12px 0 rgba(34,48,62,.08);">
                        <div class="relative" style="aspect-ratio: 4/3;">
                            @include('interstitial._ad-slot', ['ad' => $ads['bottom']])
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Trust strip --}}
        <div class="mt-8 pt-8 border-t border-[#E4E6E8] grid grid-cols-2 md:grid-cols-4 gap-6 text-xs">
            <div class="flex items-center gap-2 text-[#8592A3]">
                <svg class="w-4 h-4 text-[#71DD37]" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.53a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/></svg>
                Liên kết đã được quét virus
            </div>
            <div class="flex items-center gap-2 text-[#8592A3]">
                <svg class="w-4 h-4 text-[#696CFF]" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Không tracking cá nhân
            </div>
            <div class="flex items-center gap-2 text-[#8592A3]">
                <svg class="w-4 h-4 text-[#03C3EC]" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"/></svg>
                Kết nối an toàn HTTPS
            </div>
            <div class="flex items-center gap-2 text-[#8592A3]">
                <svg class="w-4 h-4 text-[#FFAB00]" viewBox="0 0 20 20" fill="currentColor"><path d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z"/></svg>
                Creator nhận tiền từ click này
            </div>
        </div>
    </div>
</main>

{{-- ═══════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════ --}}
<footer class="border-t border-[#E4E6E8] bg-white">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[#8592A3]">
        <span>© LinkPay · Quảng cáo giúp creator kiếm tiền. Cảm ơn 5 giây của bạn ❤️</span>
        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-[#384551]">Báo cáo quảng cáo</a>
            <a href="#" class="hover:text-[#384551]">Điều khoản</a>
            <a href="#" class="hover:text-[#696CFF] font-bold">Tắt QC Pro 49k/tháng →</a>
        </div>
    </div>
</footer>

<script>
(() => {
    const TOTAL = {{ $seconds }};
    const ringMini = document.getElementById('ring-mini');
    const cd = document.getElementById('countdown');
    const hint = document.getElementById('hint-seconds');
    const btn = document.getElementById('skip-btn');
    const lbl = document.getElementById('skip-label');
    const MINI_C = 2 * Math.PI * 16;  // ~100.5

    ringMini.setAttribute('stroke-dasharray', MINI_C);

    let c = TOTAL;
    let captchaOk = false;

    function refresh() {
        const ready = c <= 0 && captchaOk;
        if (c > 0) {
            lbl.textContent = `Đợi ${c} giây`;
            btn.classList.remove('ready');
            btn.disabled = true;
        } else if (!captchaOk) {
            lbl.textContent = 'Hoàn tất xác thực';
            btn.classList.remove('ready');
            btn.disabled = true;
        } else {
            lbl.textContent = 'Bỏ qua quảng cáo';
            btn.classList.add('ready');
            btn.disabled = false;
            // Add subtle pulsing once ready
            if (!btn.classList.contains('ready-bounce')) {
                btn.classList.add('ready-bounce');
            }
        }
    }

    const interval = setInterval(() => {
        c--;
        const display = Math.max(0, c);
        cd.textContent = display;
        if (hint) hint.textContent = display;
        ringMini.style.strokeDashoffset = (MINI_C * (TOTAL - c)) / TOTAL;
        refresh();
        if (c <= 0) {
            clearInterval(interval);
            cd.classList.remove('ticking');
        }
    }, 1000);

    window.onCaptchaPass = () => { captchaOk = true; refresh(); };

    btn.addEventListener('click', async () => {
        if (btn.disabled) return;
        btn.disabled = true;
        lbl.textContent = 'Đang chuyển...';
        document.body.style.opacity = '0.5';
        document.body.style.transition = 'opacity .3s';

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
            lbl.textContent = 'Lỗi, thử lại';
            btn.disabled = false;
            document.body.style.opacity = '1';
        }
    });

    refresh();
})();
</script>
</body>
</html>
