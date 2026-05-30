<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đang chuyển hướng... · LinkPay</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
    :root{
        --bg:#08080B; --line:rgba(255,255,255,.08); --line2:rgba(255,255,255,.16);
        --fg:#F2F2F5; --mut:#9AA0AE; --mut2:#5A6070;
        --cy:#67E8F9; --vi:#A78BFA; --pk:#ECA8D6; --am:#FBBF24; --gr:#34D399;
    }
    *{box-sizing:border-box;}
    body{margin:0;background:var(--bg);color:var(--fg);font-family:'Instrument Sans',system-ui,sans-serif;-webkit-font-smoothing:antialiased;min-height:100vh;overflow-x:hidden;}
    a{color:inherit;text-decoration:none;}
    img{display:block;}
    .mono{font-family:'JetBrains Mono',monospace;}
    .serif{font-family:'Instrument Serif',serif;font-style:italic;font-weight:400;}
    .grad{background:linear-gradient(110deg,var(--cy),var(--vi) 50%,var(--pk));-webkit-background-clip:text;background-clip:text;color:transparent;}
    .glow{position:fixed;border-radius:9999px;filter:blur(130px);pointer-events:none;z-index:0;}
    .wrapx{position:relative;z-index:10;min-height:100vh;display:flex;flex-direction:column;}
    .pillx{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line2);border-radius:999px;padding:7px 14px;font-size:12px;font-weight:600;color:var(--mut);}
    .eyebrow-chip{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);background:rgba(255,255,255,.04);border-radius:999px;padding:6px 13px;font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--mut);}
    .card-x{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:30px;position:relative;overflow:hidden;}

    /* countdown ring */
    .ring-track{stroke:rgba(255,255,255,.07);}
    .ring-prog{stroke:url(#rg);transition:stroke-dashoffset 1s linear;filter:drop-shadow(0 0 14px rgba(167,139,250,.5));}
    .cd-num{font-weight:700;letter-spacing:-.04em;font-feature-settings:'tnum';line-height:1;}
    @keyframes tick{0%{transform:scale(1);}50%{transform:scale(.95);opacity:.85;}100%{transform:scale(1);}}
    .tick{animation:tick .3s ease-out;}

    /* robot captcha */
    .robot{display:flex;align-items:center;gap:12px;width:100%;max-width:320px;padding:13px 16px;border-radius:16px;
        background:rgba(255,255,255,.03);border:1px solid var(--line2);cursor:pointer;transition:.2s;color:var(--fg);font-family:inherit;}
    .robot:hover{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.24);}
    .robot-box{width:24px;height:24px;border-radius:7px;border:2px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.25s;}
    .robot-box.ok{background:linear-gradient(135deg,var(--gr),var(--cy));border-color:transparent;}

    /* skip button */
    .skip{display:inline-flex;align-items:center;justify-content:center;gap:10px;width:100%;font-weight:700;font-size:16px;
        padding:16px 24px;border-radius:18px;border:1px solid var(--line2);background:rgba(255,255,255,.04);color:var(--mut);
        cursor:not-allowed;transition:all .35s cubic-bezier(.16,1,.3,1);font-family:inherit;}
    .skip.ready{background:linear-gradient(110deg,#A5F3FC,#C4B5FD 55%,#FBCFE8);color:#0B0B14;border-color:transparent;cursor:pointer;
        box-shadow:0 0 0 4px rgba(167,139,250,.12),0 18px 46px -10px rgba(167,139,250,.6);animation:glowpulse 2.2s ease-in-out infinite;}
    .skip.ready:hover{transform:translateY(-2px);box-shadow:0 0 0 5px rgba(167,139,250,.2),0 22px 52px -10px rgba(167,139,250,.7);}
    @keyframes glowpulse{0%,100%{box-shadow:0 0 0 4px rgba(167,139,250,.12),0 18px 46px -10px rgba(167,139,250,.6);}50%{box-shadow:0 0 0 8px rgba(167,139,250,.18),0 18px 46px -10px rgba(103,232,249,.7);}}

    .lnk{color:var(--mut);transition:.2s;}.lnk:hover{color:var(--fg);}
    @media(max-width:980px){ .stage{flex-direction:column!important;} .adcol{width:100%!important;} .nav-host{display:none!important;} }
</style>
</head>
<body>

{{-- glow blobs (landing style) --}}
<div class="glow" style="width:520px;height:520px;background:var(--vi);opacity:.16;top:-160px;left:-120px;"></div>
<div class="glow" style="width:460px;height:460px;background:var(--cy);opacity:.12;bottom:-180px;left:30%;"></div>
<div class="glow" style="width:420px;height:420px;background:var(--pk);opacity:.10;top:10%;right:-140px;"></div>

<div class="wrapx">

    {{-- ───────── NAV ───────── --}}
    <header style="padding:16px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid var(--line);">
        <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:9px;font-weight:700;font-size:17px;letter-spacing:-.01em;">
            <span style="width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,var(--cy),var(--vi));display:inline-flex;align-items:center;justify-content:center;color:#0B0B14;font-weight:800;font-size:12px;">LP</span>
            Link<span class="grad">Pay</span>
        </a>

        <div class="nav-host pillx">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--gr);box-shadow:0 0 10px var(--gr);"></span>
            <span class="mono" style="font-size:12px;color:var(--mut2);">Chuyển đến</span>
            <span class="mono" style="font-size:13px;color:var(--fg);font-weight:600;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ parse_url($link->original_url, PHP_URL_HOST) }}</span>
        </div>

        <div class="mono" style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--mut2);">
            <span style="display:none;@media(min-width:640px){display:inline;}">QC · </span>Sponsored
        </div>
    </header>

    {{-- ───────── MAIN STAGE ───────── --}}
    <main class="stage" style="flex:1;display:flex;align-items:stretch;gap:40px;padding:48px 24px;max-width:1400px;margin:0 auto;width:100%;">

        {{-- LEFT: countdown --}}
        <div style="flex:1;display:flex;align-items:center;justify-content:center;">
            <div class="card-x" style="padding:48px 40px;max-width:520px;width:100%;text-align:center;">
                <div class="glow" style="position:absolute;width:280px;height:280px;background:var(--vi);opacity:.18;top:-140px;left:50%;transform:translateX(-50%);"></div>

                <div style="position:relative;z-index:1;">
                    <div class="eyebrow-chip">
                        <span style="width:6px;height:6px;border-radius:50%;background:var(--am);" class="tw-pulse"></span>
                        Đang tải liên kết
                    </div>

                    {{-- ring + number --}}
                    <div style="margin:36px auto;position:relative;width:248px;height:248px;">
                        <svg width="248" height="248" viewBox="0 0 220 220" style="transform:rotate(-90deg);">
                            <defs>
                                <linearGradient id="rg" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#67E8F9"/>
                                    <stop offset="55%" stop-color="#A78BFA"/>
                                    <stop offset="100%" stop-color="#ECA8D6"/>
                                </linearGradient>
                            </defs>
                            <circle cx="110" cy="110" r="96" fill="none" class="ring-track" stroke-width="5"/>
                            <circle id="ring-fg" cx="110" cy="110" r="96" fill="none" class="ring-prog" stroke-width="5" stroke-linecap="round" stroke-dasharray="603.2" stroke-dashoffset="0"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <span id="countdown" class="cd-num" style="font-size:84px;">{{ $seconds }}</span>
                            <span class="mono" style="font-size:10px;letter-spacing:.3em;text-transform:uppercase;color:var(--mut2);margin-top:6px;">giây còn lại</span>
                        </div>
                    </div>

                    <p id="status-text" style="color:var(--mut);font-size:14px;margin:0 0 24px;min-height:20px;">Vui lòng chờ <strong style="color:var(--fg);">{{ $seconds }}</strong> giây và xác thực bạn không phải bot</p>

                    {{-- captcha --}}
                    <div style="display:flex;justify-content:center;margin-bottom:24px;">
                        <button type="button" id="robot-check" class="robot">
                            <span id="robot-box" class="robot-box">
                                <svg id="robot-check-icon" width="14" height="14" viewBox="0 0 20 20" fill="#0B0B14" style="display:none;">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"/>
                                </svg>
                                <svg id="robot-spinner" width="14" height="14" viewBox="0 0 24 24" fill="none" style="display:none;" class="tw-spin">
                                    <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,.7)" stroke-width="3" stroke-dasharray="32" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span id="robot-label" style="font-size:14px;font-weight:600;color:var(--fg);flex:1;text-align:left;">Tôi không phải là robot</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.3)" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- skip --}}
                    <form id="verify-form" method="POST" action="{{ route('link.verify', $link->slug) }}">
                        @csrf
                        <input type="hidden" name="impression_token" value="{{ $token }}">
                        <button id="skip-btn" type="button" disabled class="skip">
                            <span id="skip-label">Đợi {{ $seconds }} giây</span>
                            <svg id="skip-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z"/>
                            </svg>
                        </button>
                    </form>

                    <p class="mono" style="font-size:12px;color:var(--mut2);margin-top:20px;">
                        Bằng việc tiếp tục, bạn đồng ý <a href="#" class="lnk" style="text-decoration:underline;text-underline-offset:3px;">điều khoản</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT: Ad column — real brand data from JSON content field --}}
        <div class="adcol" style="width:440px;display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="mono" style="font-size:10px;letter-spacing:.25em;text-transform:uppercase;color:var(--mut2);">Quảng cáo</span>
                <a href="#" class="mono lnk" style="font-size:10px;">Tại sao tôi thấy quảng cáo?</a>
            </div>

            @php
                $parseAd = function($ad) use ($token) {
                    if (!$ad) return null;
                    $meta = json_decode($ad->content ?? '', true);
                    if (!is_array($meta) || !isset($meta['image'])) return null;
                    return [
                        'id'       => $ad->id,
                        'image'    => $meta['image'],
                        'headline' => $meta['headline'],
                        'sub'      => $meta['sub'],
                        'cta'      => $meta['cta'],
                        'brand'    => $meta['brand'],
                        'color'    => $meta['color'],
                        'target'   => route('ad.click', ['campaign' => $ad->id, 'token' => $token]),
                    ];
                };
                $topAd = $parseAd($ads['top']);
                $sideAd = $parseAd($ads['side']);
                $bottomAd = $parseAd($ads['bottom']);
            @endphp

            {{-- Featured top ad --}}
            @if($topAd)
                <a href="{{ $topAd['target'] }}" target="_blank" rel="noopener"
                   style="display:block;border-radius:24px;overflow:hidden;position:relative;box-shadow:0 12px 40px -10px rgba(0,0,0,.6);border:1px solid var(--line);" class="group">
                    <div style="position:relative;aspect-ratio:4/3;">
                        <img src="{{ asset(ltrim($topAd['image'], '/')) }}" alt="{{ $topAd['brand'] }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                        <div style="position:absolute;inset:0;background:linear-gradient(135deg,{{ $topAd['color'] }}E6 0%,{{ $topAd['color'] }}99 50%,transparent 100%);"></div>
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.6),transparent 60%);"></div>
                        <div style="position:absolute;top:16px;left:16px;right:16px;display:flex;align-items:center;justify-content:space-between;">
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:999px;background:#fff;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:{{ $topAd['color'] }};">
                                <span style="width:6px;height:6px;border-radius:50%;background:{{ $topAd['color'] }};"></span>{{ $topAd['brand'] }}
                            </span>
                            <span class="mono" style="padding:4px 9px;border-radius:999px;background:rgba(0,0,0,.4);backdrop-filter:blur(8px);font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.8);font-weight:700;">Featured</span>
                        </div>
                        <div style="position:absolute;bottom:20px;left:20px;right:20px;">
                            <div style="color:#fff;font-size:26px;font-weight:800;letter-spacing:-.02em;line-height:1;text-shadow:0 2px 12px rgba(0,0,0,.5);">{{ $topAd['headline'] }}</div>
                            <div style="color:rgba(255,255,255,.95);font-size:12px;margin-top:8px;font-weight:500;">{{ $topAd['sub'] }}</div>
                            <div style="margin-top:16px;display:inline-flex;align-items:center;gap:6px;padding:9px 15px;border-radius:999px;background:#fff;font-size:12px;font-weight:800;color:{{ $topAd['color'] }};">
                                {{ $topAd['cta'] }}
                                <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endif

            {{-- Two smaller ads --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                @foreach ([$sideAd, $bottomAd] as $small)
                    @if($small)
                        <a href="{{ $small['target'] }}" target="_blank" rel="noopener"
                           style="display:block;border-radius:18px;overflow:hidden;position:relative;box-shadow:0 6px 20px -6px rgba(0,0,0,.5);border:1px solid var(--line);">
                            <div style="position:relative;aspect-ratio:1/1;">
                                <img src="{{ asset(ltrim($small['image'], '/')) }}" alt="{{ $small['brand'] }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                                <div style="position:absolute;inset:0;background:linear-gradient(180deg,transparent 0%,{{ $small['color'] }}AA 70%,{{ $small['color'] }}EE 100%);"></div>
                                <div style="position:absolute;top:9px;left:9px;padding:3px 8px;border-radius:999px;background:#fff;font-size:9px;font-weight:800;text-transform:uppercase;color:{{ $small['color'] }};">{{ $small['brand'] }}</div>
                                <div style="position:absolute;bottom:12px;left:12px;right:12px;">
                                    <div style="color:#fff;font-size:14px;font-weight:800;letter-spacing:-.01em;line-height:1.05;text-shadow:0 1px 8px rgba(0,0,0,.5);">{{ $small['headline'] }}</div>
                                    <div style="color:rgba(255,255,255,.9);font-size:10px;margin-top:4px;font-weight:500;">{{ $small['sub'] }}</div>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="mono" style="margin-top:auto;padding-top:16px;display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--mut2);">
                <span style="display:flex;align-items:center;gap:6px;">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="var(--gr)"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001z"/></svg>
                    Link đã quét virus
                </span>
                <span>Creator nhận tiền từ click này</span>
            </div>
        </div>
    </main>

    {{-- ───────── FOOTER ───────── --}}
    <footer class="mono" style="padding:18px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:11px;color:var(--mut2);border-top:1px solid var(--line);">
        <span>© LinkPay · Cảm ơn {{ $seconds }} giây của bạn ❤️</span>
        <div style="display:flex;align-items:center;gap:18px;">
            <a href="#" class="lnk">Báo cáo</a>
            <a href="#" class="lnk">Điều khoản</a>
            <a href="{{ route('premium.index') }}" class="lnk" style="color:var(--am);font-weight:700;">Tắt QC với Premium →</a>
        </div>
    </footer>
</div>

<style>
    @keyframes tw-pulse{0%,100%{opacity:1;}50%{opacity:.35;}}
    .tw-pulse{animation:tw-pulse 1.4s ease-in-out infinite;}
    @keyframes tw-spin{to{transform:rotate(360deg);}}
    .tw-spin{animation:tw-spin .8s linear infinite;}
</style>

<script>
(() => {
    const TOTAL = {{ $seconds }};
    const ringFg = document.getElementById('ring-fg');
    const cd = document.getElementById('countdown');
    const status = document.getElementById('status-text');
    const btn = document.getElementById('skip-btn');
    const lbl = document.getElementById('skip-label');
    const RING_C = 2 * Math.PI * 96;

    ringFg.setAttribute('stroke-dasharray', RING_C);
    ringFg.style.strokeDashoffset = 0;

    let c = TOTAL;
    let captchaOk = false;
    const G = '#34D399', A = '#FBBF24';

    function refresh() {
        if (c > 0 && !captchaOk) {
            lbl.textContent = `Đợi ${c} giây`;
            status.innerHTML = `Vui lòng chờ <strong style="color:#F2F2F5">${c}</strong> giây và xác thực bạn không phải bot`;
            btn.classList.remove('ready'); btn.disabled = true;
        } else if (c > 0 && captchaOk) {
            lbl.textContent = `Đợi ${c} giây`;
            status.innerHTML = `<span style="color:${G}">✓</span> Xác thực thành công · Chờ thêm <strong style="color:#F2F2F5">${c}</strong> giây`;
            btn.classList.remove('ready'); btn.disabled = true;
        } else if (c <= 0 && !captchaOk) {
            lbl.textContent = 'Hoàn tất xác thực để tiếp tục';
            status.innerHTML = `<span style="color:${A}">↓</span> Vui lòng hoàn thành xác thực bên dưới`;
            btn.classList.remove('ready'); btn.disabled = true;
        } else {
            lbl.textContent = 'Bỏ qua · Đến đích';
            status.innerHTML = `<span style="color:${G}">✓</span> Sẵn sàng chuyển hướng`;
            btn.classList.add('ready'); btn.disabled = false;
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

    // captcha
    const robotCheck = document.getElementById('robot-check');
    const robotBox = document.getElementById('robot-box');
    const robotIcon = document.getElementById('robot-check-icon');
    const robotSpinner = document.getElementById('robot-spinner');
    const robotLabel = document.getElementById('robot-label');
    let captchaToken = '';

    robotCheck.addEventListener('click', () => {
        if (captchaOk || robotCheck.disabled) return;
        robotCheck.disabled = true;
        robotSpinner.style.display = 'block';
        robotLabel.textContent = 'Đang xác thực...';
        setTimeout(() => {
            robotSpinner.style.display = 'none';
            robotIcon.style.display = 'block';
            robotBox.classList.add('ok');
            robotLabel.innerHTML = '<span style="color:#34D399">✓</span> Đã xác thực thành công';
            captchaToken = 'demo-' + Math.random().toString(36).slice(2);
            captchaOk = true;
            refresh();
        }, 400 + Math.random() * 300);
    });

    btn.addEventListener('click', async () => {
        if (btn.disabled) return;
        btn.disabled = true;
        lbl.textContent = 'Đang chuyển hướng...';
        document.body.style.opacity = '0.4';
        document.body.style.transition = 'opacity .3s';
        const fd = new FormData(document.getElementById('verify-form'));
        if (captchaToken) fd.append('cf-turnstile-response', captchaToken);
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
