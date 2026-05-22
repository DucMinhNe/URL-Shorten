<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đang chuyển hướng... · LinkPay</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<style>
    :root {
        --ink-darkest: #07070C;
        --ink-deep: #0E0E14;
        --ink: #161620;
        --ink-line: #232331;
        --ink-card: rgba(22, 22, 32, .72);
        --brand: #696CFF;
        --brand-deep: #4B4ECF;
        --gold: #FFD60A;
        --gold-deep: #FFAB00;
    }
    html, body { font-family: 'Public Sans', system-ui, sans-serif; }
    body {
        background: #07070C;
        color: #E7E7EE;
        min-height: 100vh;
    }

    .mono { font-family: 'JetBrains Mono', monospace; }

    /* Mesh gradient background */
    .mesh-bg {
        position: fixed; inset: 0; z-index: 0;
        background:
            radial-gradient(at 20% 10%, rgba(105,108,255,.25) 0px, transparent 50%),
            radial-gradient(at 80% 80%, rgba(255,171,0,.12) 0px, transparent 50%),
            radial-gradient(at 60% 30%, rgba(255,62,29,.08) 0px, transparent 50%),
            #07070C;
    }
    .grid-overlay {
        position: fixed; inset: 0; z-index: 1;
        background-image:
            linear-gradient(to right, rgba(255,255,255,.02) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255,255,255,.02) 1px, transparent 1px);
        background-size: 64px 64px;
        pointer-events: none;
    }

    /* Glass card */
    .glass {
        background: linear-gradient(180deg, rgba(255,255,255,.04) 0%, rgba(255,255,255,.01) 100%);
        border: 1px solid rgba(255,255,255,.06);
        backdrop-filter: blur(20px) saturate(1.2);
        -webkit-backdrop-filter: blur(20px) saturate(1.2);
        box-shadow:
            0 24px 60px -12px rgba(0,0,0,.8),
            0 0 0 1px rgba(255,255,255,.04),
            inset 0 1px 0 rgba(255,255,255,.05);
    }

    /* Countdown ring */
    .ring-bg { stroke: rgba(255,255,255,.06); }
    .ring-fg { stroke: url(#ringGradient); transition: stroke-dashoffset 1s linear; filter: drop-shadow(0 0 12px rgba(105,108,255,.4)); }

    /* Skip button */
    .skip-btn {
        position: relative;
        background: linear-gradient(180deg, #2A2A38 0%, #1A1A24 100%);
        color: rgba(255,255,255,.4);
        cursor: not-allowed;
        border: 1px solid rgba(255,255,255,.08);
        transition: all .4s cubic-bezier(.16,1,.3,1);
    }
    .skip-btn.ready {
        background: linear-gradient(135deg, #FFD60A 0%, #FFAB00 100%);
        color: #0A0A0F;
        cursor: pointer;
        border: 1px solid #FFD60A;
        box-shadow:
            0 0 0 4px rgba(255,214,10,.15),
            0 8px 32px -6px rgba(255,171,0,.6),
            inset 0 1px 0 rgba(255,255,255,.4);
        animation: ready-glow 2s ease-in-out infinite;
    }
    .skip-btn.ready:hover {
        transform: translateY(-2px);
        box-shadow:
            0 0 0 4px rgba(255,214,10,.25),
            0 12px 36px -6px rgba(255,171,0,.7),
            inset 0 1px 0 rgba(255,255,255,.4);
    }
    @keyframes ready-glow {
        0%, 100% { box-shadow: 0 0 0 4px rgba(255,214,10,.15), 0 8px 32px -6px rgba(255,171,0,.6), inset 0 1px 0 rgba(255,255,255,.4); }
        50% { box-shadow: 0 0 0 8px rgba(255,214,10,.2), 0 8px 32px -6px rgba(255,171,0,.8), inset 0 1px 0 rgba(255,255,255,.4); }
    }

    /* Number tick */
    @keyframes count-tick {
        0% { transform: scale(1); }
        50% { transform: scale(.96); opacity: .8; }
        100% { transform: scale(1); }
    }
    .tick { animation: count-tick .3s ease-out; }

    /* Ad card */
    .ad-card {
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        backdrop-filter: blur(12px);
    }
    .ad-card:hover { border-color: rgba(255,255,255,.12); }

    /* Twinkle stars */
    @keyframes twinkle {
        0%, 100% { opacity: .3; transform: scale(.8); }
        50% { opacity: 1; transform: scale(1.2); }
    }
    .star { position: absolute; width: 2px; height: 2px; background: white; border-radius: 50%; }
</style>
</head>
<body>

<div class="mesh-bg"></div>
<div class="grid-overlay"></div>

{{-- Decorative stars --}}
<div class="fixed inset-0 z-[1] pointer-events-none">
    @foreach ([[10,15,2],[18,40,3],[30,8,2.5],[50,80,2],[70,20,4],[85,60,2.5],[92,90,3],[5,75,2]] as $i => $s)
        <div class="star" style="top: {{ $s[0] }}%; left: {{ $s[1] }}%; animation: twinkle {{ $s[2] }}s ease-in-out infinite; animation-delay: {{ $i * .3 }}s;"></div>
    @endforeach
</div>

<div class="relative z-10 min-h-screen flex flex-col">

    {{-- ───────── TOP NAV STRIP ───────── --}}
    <header class="px-4 lg:px-8 py-5 flex items-center justify-between gap-4 border-b border-white/[.04]">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                <rect x="2" y="2" width="28" height="28" rx="8" fill="#fff"/>
                <path d="M11 18.5 L21 13.5" stroke="#696CFF" stroke-width="2.8" stroke-linecap="round"/>
                <circle cx="11" cy="18.5" r="3.2" fill="#696CFF"/>
                <circle cx="21" cy="13.5" r="3.2" fill="#696CFF"/>
            </svg>
            <span class="font-bold text-white text-base tracking-tight">LinkPay</span>
        </a>

        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/[.04] border border-white/[.06]">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400" style="animation: twinkle 1.4s ease-in-out infinite;"></span>
            <span class="text-xs text-white/40 mono">Chuyển đến</span>
            <span class="text-sm text-white font-semibold mono truncate max-w-[300px]">{{ parse_url($link->original_url, PHP_URL_HOST) }}</span>
        </div>

        <div class="text-[10px] mono uppercase tracking-[0.2em] text-white/30">
            <span class="hidden sm:inline">QC · </span>Sponsored
        </div>
    </header>

    {{-- ───────── MAIN STAGE ───────── --}}
    <main class="flex-1 flex flex-col lg:flex-row items-stretch gap-6 lg:gap-10 px-4 lg:px-10 py-8 lg:py-14 max-w-[1500px] mx-auto w-full">

        {{-- LEFT: Countdown stage (focal point) --}}
        <div class="lg:flex-1 flex items-center justify-center">
            <div class="glass rounded-[32px] p-8 lg:p-12 max-w-[520px] w-full text-center relative overflow-hidden">

                {{-- Decorative top glow --}}
                <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-64 h-64 rounded-full bg-[#696CFF] opacity-20 blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/[.06] border border-white/[.06] text-[10px] uppercase tracking-[0.2em] text-white/60 mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFD60A] animate-pulse"></span>
                        Đang tải liên kết
                    </div>

                    {{-- Big circular countdown --}}
                    <div class="my-8 lg:my-10 inline-block relative">
                        <svg class="w-56 h-56 lg:w-64 lg:h-64 transform -rotate-90" viewBox="0 0 220 220">
                            <defs>
                                <linearGradient id="ringGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#9B9DFF"/>
                                    <stop offset="50%" stop-color="#696CFF"/>
                                    <stop offset="100%" stop-color="#FFD60A"/>
                                </linearGradient>
                            </defs>
                            <circle cx="110" cy="110" r="96" fill="none" class="ring-bg" stroke-width="6"/>
                            <circle id="ring-fg" cx="110" cy="110" r="96" fill="none" class="ring-fg" stroke-width="6" stroke-linecap="round"
                                    stroke-dasharray="603.2" stroke-dashoffset="0"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span id="countdown" class="text-7xl lg:text-8xl font-black text-white" style="font-feature-settings: 'tnum';">{{ $seconds }}</span>
                            <span class="text-[10px] mono uppercase tracking-[0.3em] text-white/40 mt-2">giây còn lại</span>
                        </div>
                    </div>

                    {{-- Status text --}}
                    <p id="status-text" class="text-white/70 text-sm mb-6 min-h-[20px]">Vui lòng chờ <strong class="text-white">{{ $seconds }}</strong> giây và xác thực bạn không phải bot</p>

                    {{-- Captcha --}}
                    <div class="flex justify-center mb-6">
                        <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-callback="onCaptchaPass" data-theme="dark"></div>
                    </div>

                    {{-- Skip button --}}
                    <form id="verify-form" method="POST" action="{{ route('link.verify', $link->slug) }}">
                        @csrf
                        <input type="hidden" name="impression_token" value="{{ $token }}">
                        <button id="skip-btn" type="button" disabled
                                class="skip-btn w-full font-bold text-base px-8 py-4 rounded-2xl flex items-center justify-center gap-2.5">
                            <span id="skip-label">Đợi {{ $seconds }} giây</span>
                            <svg id="skip-icon" class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z"/>
                                <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z"/>
                            </svg>
                        </button>
                    </form>

                    <p class="text-xs text-white/30 mt-5 mono">
                        Bằng việc tiếp tục, bạn đồng ý <a href="#" class="text-white/60 hover:text-white underline-offset-4 underline">điều khoản</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT: Ad column --}}
        <div class="lg:w-[420px] xl:w-[480px] flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] mono uppercase tracking-[0.25em] text-white/30">Quảng cáo</span>
                <a href="#" class="text-[10px] mono text-white/30 hover:text-white/60">Tại sao tôi thấy quảng cáo?</a>
            </div>

            {{-- Top featured ad --}}
            @if($ads['top'])
                <a href="{{ $ads['top']->target_url ?? '#' }}" target="_blank" rel="noopener"
                   class="ad-card block rounded-2xl overflow-hidden group transition-all">
                    <div class="relative" style="aspect-ratio: 4/3;">
                        <img src="{{ $ads['top']->content }}" alt="{{ $ads['top']->name }}" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                        <div class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-white/10 backdrop-blur-md text-[10px] mono uppercase tracking-wider text-white/80">Featured</div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="text-xs text-white/60 mono uppercase tracking-wider">{{ $ads['top']->name }}</div>
                            <div class="text-white font-bold text-lg mt-1 leading-tight">Khám phá ưu đãi đặc biệt từ đối tác của chúng tôi</div>
                            <div class="mt-3 inline-flex items-center gap-1.5 text-[#FFD60A] text-sm font-semibold group-hover:gap-2.5 transition-all">
                                Tìm hiểu ngay
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endif

            {{-- Two smaller ads --}}
            <div class="grid grid-cols-2 gap-3">
                @foreach (['side', 'bottom'] as $slot)
                    @if($ads[$slot])
                        <a href="{{ $ads[$slot]->target_url ?? '#' }}" target="_blank" rel="noopener"
                           class="ad-card block rounded-xl overflow-hidden transition-all">
                            <div class="relative" style="aspect-ratio: 1/1;">
                                <img src="{{ $ads[$slot]->content }}" alt="{{ $ads[$slot]->name }}" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-2 left-2 right-2">
                                    <div class="text-[10px] mono uppercase tracking-wider text-white/50">{{ Str::words($ads[$slot]->name, 2, '') }}</div>
                                    <div class="text-white text-xs font-semibold mt-0.5 leading-tight line-clamp-2">{{ $ads[$slot]->name }}</div>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Stats badge below ads --}}
            <div class="mt-auto pt-4 flex items-center justify-between text-[11px] text-white/40 mono">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3 h-3 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001z"/></svg>
                    Link đã quét virus
                </span>
                <span>Creator nhận tiền từ click này</span>
            </div>
        </div>
    </main>

    {{-- ───────── FOOTER ───────── --}}
    <footer class="px-4 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] mono text-white/30 border-t border-white/[.04]">
        <span>© LinkPay · Cảm ơn 5 giây của bạn ❤️</span>
        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-white/70">Báo cáo</a>
            <a href="#" class="hover:text-white/70">Điều khoản</a>
            <a href="#" class="hover:text-[#FFD60A] font-bold">Tắt QC Pro 49k/tháng →</a>
        </div>
    </footer>
</div>

<script>
(() => {
    const TOTAL = {{ $seconds }};
    const ringFg = document.getElementById('ring-fg');
    const cd = document.getElementById('countdown');
    const status = document.getElementById('status-text');
    const btn = document.getElementById('skip-btn');
    const lbl = document.getElementById('skip-label');
    const RING_C = 2 * Math.PI * 96;  // ~603.2

    ringFg.setAttribute('stroke-dasharray', RING_C);
    ringFg.style.strokeDashoffset = 0;

    let c = TOTAL;
    let captchaOk = false;

    function refresh() {
        if (c > 0 && !captchaOk) {
            lbl.textContent = `Đợi ${c} giây`;
            status.innerHTML = `Vui lòng chờ <strong class="text-white">${c}</strong> giây và xác thực bạn không phải bot`;
            btn.classList.remove('ready');
            btn.disabled = true;
        } else if (c > 0 && captchaOk) {
            lbl.textContent = `Đợi ${c} giây`;
            status.innerHTML = `<span class="text-green-400">✓</span> Xác thực thành công · Chờ thêm <strong class="text-white">${c}</strong> giây`;
            btn.classList.remove('ready');
            btn.disabled = true;
        } else if (c <= 0 && !captchaOk) {
            lbl.textContent = 'Hoàn tất xác thực để tiếp tục';
            status.innerHTML = '<span class="text-[#FFD60A]">↓</span> Vui lòng hoàn thành captcha bên dưới';
            btn.classList.remove('ready');
            btn.disabled = true;
        } else {
            lbl.textContent = 'Bỏ qua quảng cáo · Đến đích';
            status.innerHTML = '<span class="text-green-400">✓</span> Sẵn sàng chuyển hướng';
            btn.classList.add('ready');
            btn.disabled = false;
        }
    }

    const interval = setInterval(() => {
        c--;
        cd.textContent = Math.max(0, c);
        cd.classList.add('tick');
        setTimeout(() => cd.classList.remove('tick'), 300);
        ringFg.style.strokeDashoffset = (RING_C * (TOTAL - c)) / TOTAL;
        refresh();
        if (c <= 0) clearInterval(interval);
    }, 1000);

    window.onCaptchaPass = () => { captchaOk = true; refresh(); };

    btn.addEventListener('click', async () => {
        if (btn.disabled) return;
        btn.disabled = true;
        lbl.textContent = 'Đang chuyển hướng...';
        document.body.style.opacity = '0.4';
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
