<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
@php $pageTitle = 'LinkPay — Mỗi click là tiền'; @endphp
<title>{{ $pageTitle }}</title>
<x-seo :title="$pageTitle"
       description="Rút gọn link miễn phí và kiếm tiền theo mỗi lượt view hợp lệ. Tạo link có alias, mật khẩu, QR code và thống kê chi tiết. Rút về MoMo, ZaloPay, PayPal trong 24h."/>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    :root{
        --bg:#08080B; --line:rgba(255,255,255,.08); --line2:rgba(255,255,255,.16);
        --fg:#F2F2F5; --mut:#9AA0AE; --mut2:#6B7280;
        --cy:#67E8F9; --vi:#A78BFA; --pk:#ECA8D6; --am:#FBBF24; --gr:#34D399;
    }
    *{box-sizing:border-box;}
    html{scroll-behavior:smooth;}
    body{margin:0;background:var(--bg);color:var(--fg);font-family:'Instrument Sans',system-ui,sans-serif;-webkit-font-smoothing:antialiased;overflow-x:hidden;}
    a{color:inherit;text-decoration:none;}
    img,video{display:block;}
    .wrap{max-width:1240px;margin:0 auto;padding:0 28px;}
    .serif{font-family:'Instrument Serif',serif;font-style:italic;font-weight:400;}
    .mono{font-family:'JetBrains Mono',monospace;}
    .grad{background:linear-gradient(110deg,var(--cy),var(--vi) 50%,var(--pk));-webkit-background-clip:text;background-clip:text;color:transparent;}
    .eyebrow{font-size:12px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--mut);display:inline-flex;align-items:center;gap:14px;}
    .eyebrow::before{content:"";width:32px;height:1px;background:var(--line2);}
    .display{font-size:clamp(40px,7vw,84px);font-weight:700;line-height:.98;letter-spacing:-.03em;margin:0;}
    .mut2{color:#5A6070;}
    .card{background:rgba(255,255,255,.02);border:1px solid var(--line);border-radius:22px;transition:.3s;position:relative;overflow:hidden;}
    .card:hover{border-color:var(--line2);background:rgba(255,255,255,.045);}
    .btn{display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:14px;border-radius:999px;padding:13px 24px;transition:.25s;cursor:pointer;border:0;}
    .btn-primary{background:#F2F2F5;color:#0B0B14;}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 16px 40px -10px rgba(255,255,255,.35);}
    .btn-grad{background:linear-gradient(110deg,#A5F3FC,#C4B5FD 55%,#FBCFE8);color:#0B0B14;}
    .btn-grad:hover{transform:translateY(-2px);box-shadow:0 16px 44px -8px rgba(167,139,250,.6);}
    .btn-ghost{border:1px solid var(--line2);color:var(--fg);background:transparent;}
    .btn-ghost:hover{background:rgba(255,255,255,.06);}
    .pill{display:inline-flex;align-items:center;gap:7px;border:1px solid var(--line2);border-radius:999px;padding:6px 13px;font-size:12px;font-weight:600;color:var(--mut);}
    .glow{position:absolute;border-radius:9999px;filter:blur(130px);pointer-events:none;}
    .navlink{color:var(--mut);font-size:14px;font-weight:600;padding:8px 14px;border-radius:999px;transition:.2s;}
    .navlink:hover{color:var(--fg);}
    details summary{list-style:none;cursor:pointer;}
    details summary::-webkit-details-marker{display:none;}
    details[open] .plus{transform:rotate(45deg);}
    .grid-auto{display:grid;gap:20px;}
    @media(max-width:880px){
        .nav-menu,.nav-status{display:none!important;}
        .hero-grid{grid-template-columns:1fr!important;}
        .hero-media{display:none!important;}
        .two-col{grid-template-columns:1fr!important;}
    }
</style>
</head>
<body>

@php
    $compact = function ($n) {
        if ($n >= 1000000) return rtrim(rtrim(number_format($n/1000000,1),'0'),'.').'M';
        if ($n >= 1000) return rtrim(rtrim(number_format($n/1000,1),'0'),'.').'K';
        return number_format($n);
    };
@endphp

{{-- ══════════ NAV ══════════ --}}
<header style="position:fixed;top:0;left:0;right:0;z-index:50;padding:14px 0;">
    <div class="wrap">
        <div style="display:flex;align-items:center;gap:16px;justify-content:space-between;border:1px solid var(--line);border-radius:999px;background:rgba(8,8,11,.7);backdrop-filter:blur(16px);padding:10px 12px 10px 22px;">
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:9px;font-weight:700;letter-spacing:-.01em;font-size:17px;">
                <span style="width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,var(--cy),var(--vi));display:inline-flex;align-items:center;justify-content:center;color:#0B0B14;font-weight:800;font-size:12px;">LP</span>
                Link<span class="grad">Pay</span>
            </a>
            <div class="nav-status mono" style="display:flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:999px;padding:6px 13px;font-size:11px;color:var(--mut);">
                <span style="width:6px;height:6px;border-radius:999px;background:var(--gr);box-shadow:0 0 8px var(--gr);"></span>
                Tất cả link hoạt động · {{ number_format($stats['active_links'] ?? 0) }} link
            </div>
            <nav class="nav-menu" style="display:flex;align-items:center;gap:2px;margin-left:auto;">
                <a class="navlink" href="#features">Tính năng</a>
                <a class="navlink" href="#how">Cách hoạt động</a>
                <a class="navlink" href="#pay">Thanh toán</a>
                <a class="navlink" href="{{ route('faq') }}">FAQ</a>
                <a class="navlink" href="{{ route('leaderboard') }}">Bảng xếp hạng</a>
            </nav>
            <div style="display:flex;align-items:center;gap:8px;">
                @auth
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Vào tổng quan</a>
                @else
                    <a class="navlink" href="{{ route('login') }}" style="color:var(--fg);">Đăng nhập</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Bắt đầu</a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- ══════════ HERO ══════════ --}}
<section style="position:relative;min-height:100vh;display:flex;align-items:center;overflow:hidden;padding:140px 0 70px;background:#08080B;">
    {{-- tree media on the right --}}
    <div class="hero-media" style="position:absolute;top:0;right:0;width:55%;height:100%;">
        <video autoplay muted loop playsinline preload="none" style="width:100%;height:100%;object-fit:cover;">
            <source src="{{ asset('images/fink/bg-hero.mp4') }}" type="video/mp4">
        </video>
        <div style="position:absolute;inset:0;background:linear-gradient(90deg,#08080B 2%,transparent 45%),linear-gradient(180deg,transparent 60%,#08080B);"></div>
    </div>
    <div class="glow" style="bottom:-120px;left:-60px;width:420px;height:420px;background:var(--vi);opacity:.14;"></div>

    <div class="wrap" style="position:relative;width:100%;">
        <div class="hero-grid" style="display:grid;grid-template-columns:1fr;">
            <div style="max-width:620px;">
                <div class="eyebrow" style="margin-bottom:26px;">Rút gọn link · trả tiền mỗi view hợp lệ</div>
                <h1 class="display">Mỗi click là tiền.<br><span class="mut2">Link của bạn,</span> <span class="serif" style="color:var(--pk);">lương của bạn.</span></h1>
                <p style="font-size:18px;color:var(--mut);max-width:500px;margin:26px 0 0;line-height:1.65;">
                    Chỉ với 1 thao tác dán link. Mỗi <strong style="color:var(--fg);">1.000 view hợp lệ</strong> nhận tiền vào ví — rút qua MoMo · ZaloPay · PayPal trong 24h.
                </p>

                {{-- code-style transform block --}}
                <div class="mono" style="margin-top:28px;border:1px solid var(--line);border-radius:14px;background:rgba(0,0,0,.5);overflow:hidden;max-width:520px;backdrop-filter:blur(4px);">
                    <div style="display:flex;align-items:center;gap:7px;padding:11px 14px;border-bottom:1px solid var(--line);">
                        <span style="width:10px;height:10px;border-radius:999px;background:#FB7185;"></span>
                        <span style="width:10px;height:10px;border-radius:999px;background:#FBBF24;"></span>
                        <span style="width:10px;height:10px;border-radius:999px;background:#34D399;"></span>
                        <span style="margin-left:auto;font-size:11px;color:var(--mut);">linkpay.vn</span>
                    </div>
                    <div style="padding:15px;font-size:12.5px;line-height:2;">
                        <div style="color:#7F8694;"><span style="color:#FB7185;">−</span> example.com/bai-viet-rat-dai/2026?ref=fb&utm=...</div>
                        <div style="color:var(--cy);"><span style="color:var(--gr);">+</span> linkpay.vn/kiem-tien <span style="color:#7F8694;"># mỗi view = tiền</span></div>
                    </div>
                </div>

                @if(session('shortUrl'))
                    <div style="margin-top:16px;padding:13px 15px;border-radius:12px;background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.35);display:flex;align-items:center;gap:11px;max-width:520px;">
                        <span style="font-size:13px;font-weight:700;color:var(--gr);white-space:nowrap;">✓ Đã rút gọn:</span>
                        <code class="mono" style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--fg);font-size:13px;">{{ session('shortUrl') }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ session('shortUrl') }}');this.textContent='Đã copy'" style="background:var(--gr);color:#06281C;border:0;border-radius:999px;padding:7px 13px;font-weight:700;font-size:12px;cursor:pointer;">Copy</button>
                    </div>
                @endif

                <form method="POST" action="{{ route('shorten.guest') }}" style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap;max-width:520px;">
                    @csrf
                    <div style="flex:1;min-width:230px;display:flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid var(--line2);border-radius:999px;padding:4px 4px 4px 18px;">
                        <input name="original_url" type="url" required value="{{ old('original_url') }}" placeholder="Dán liên kết dài của bạn..."
                               style="flex:1;min-width:0;background:transparent;border:0;outline:none;color:var(--fg);font-size:15px;padding:11px 0;font-family:inherit;">
                    </div>
                    <button type="submit" class="btn btn-grad" style="padding:14px 26px;">Rút gọn ngay →</button>
                </form>
                @error('original_url')<p style="color:#FCA5A5;font-size:14px;margin-top:10px;">{{ $message }}</p>@enderror

                @php
                    $metrics = [
                        [$compact($stats['valid_views'] ?? 0).'+','View hợp lệ đã trả'],
                        [number_format($stats['rate_per_1000'] ?? 5000).'đ','Mỗi 1.000 view'],
                        ['< 24h','Duyệt rút tiền'],
                        [number_format($stats['active_links'] ?? 0),'Link đang chạy'],
                    ];
                @endphp
                <div style="display:grid;grid-template-columns:repeat(4,auto);gap:14px 36px;margin-top:46px;">
                    @foreach($metrics as $m)
                        <div>
                            <div class="grad" style="font-size:26px;font-weight:700;letter-spacing:-.02em;">{{ $m[0] }}</div>
                            <div style="font-size:11px;color:var(--mut);text-transform:uppercase;letter-spacing:.06em;margin-top:3px;">{{ $m[1] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════ FEATURES ══════════ --}}
<section id="features" style="padding:120px 0;position:relative;">
    <div class="wrap">
        <div class="two-col" style="display:grid;grid-template-columns:1.3fr 1fr;gap:30px;align-items:end;margin-bottom:64px;">
            <div>
                <div class="eyebrow" style="margin-bottom:24px;">Tính năng</div>
                <h2 class="display" style="font-size:clamp(36px,5.5vw,68px);">Định tuyến link<br><span class="mut2">chuẩn công nghiệp.</span></h2>
            </div>
            <p style="color:var(--mut);font-size:17px;line-height:1.65;">Thiết kế cho creator cần độ tin cậy và tốc độ tối đa — chống gian lận, bảo mật, thống kê tới từng click.</p>
        </div>
        @php
            $feats = [
                ['01','var(--cy)','Chống gian lận','Cloudflare Turnstile + dedup IP 24h chặn bot và tự click. Chỉ view hợp lệ mới được tính tiền — minh bạch tuyệt đối.','Chống bot'],
                ['02','var(--vi)','Mật khẩu link','Bảo vệ link nhạy cảm bằng passcode. Chỉ người có mật khẩu mới mở được liên kết của bạn.','Bảo mật'],
                ['03','var(--pk)','Alias + QR code','linkpay.vn/ten-de-nho thay cho chuỗi random, kèm QR code tải về dùng ngay trên poster, story.','Dễ chia sẻ'],
                ['04','var(--am)','Analytics chi tiết','Click theo ngày, thiết bị, trình duyệt, nguồn truy cập — biểu đồ trực quan cho từng link.','Realtime'],
            ];
        @endphp
        <div class="grid-auto" style="grid-template-columns:repeat(auto-fit,minmax(255px,1fr));">
            @foreach($feats as $f)
                <div class="card" style="padding:30px;min-height:300px;display:flex;flex-direction:column;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:auto;">
                        <span class="mono" style="font-size:13px;color:var(--mut2);">{{ $f[0] }}</span>
                        <span class="pill" style="border-color:{{ $f[1] }}55;color:{{ $f[1] }};">{{ $f[4] }}</span>
                    </div>
                    <div style="width:42px;height:42px;border-radius:11px;background:{{ $f[1] }}1A;border:1px solid {{ $f[1] }}44;margin:34px 0 18px;display:flex;align-items:center;justify-content:center;">
                        <span style="width:12px;height:12px;border-radius:4px;background:{{ $f[1] }};box-shadow:0 0 16px {{ $f[1] }};"></span>
                    </div>
                    <h3 style="font-size:20px;font-weight:700;margin:0;">{{ $f[2] }}</h3>
                    <p style="color:var(--mut);font-size:14px;margin-top:10px;line-height:1.6;">{{ $f[3] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════ HOW IT WORKS ══════════ --}}
<section id="how" style="padding:120px 0;background:#000;position:relative;overflow:hidden;">
    <div class="wrap two-col" style="position:relative;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;">
        <div>
            <div class="eyebrow" style="margin-bottom:24px;">Cách hoạt động</div>
            <h2 class="display" style="font-size:clamp(34px,5vw,60px);">Ba bước.<br><span class="serif" style="color:var(--pk);">Tiền vào ví.</span></h2>
            <p style="color:var(--mut);font-size:17px;line-height:1.65;margin-top:20px;max-width:420px;">Từ liên kết tới thu nhập chỉ trong vài giây — không cần kỹ thuật, không phí khởi tạo.</p>
        </div>
        <div style="text-align:center;">
            <img src="{{ asset('images/fink/tree.png') }}" alt="Minh hoạ cách rút gọn link kiếm tiền" width="1554" height="1664" loading="lazy" style="max-width:100%;height:auto;mix-blend-mode:screen;">
        </div>
    </div>

    @php
        $steps = [
            ['01','Tạo link','dán URL, đặt alias','Dán URL gốc, đặt alias hoặc mật khẩu nếu muốn. Link ngắn được tạo ra ngay trong 1 giây.', true],
            ['02','Chia sẻ','lan toả mọi nền tảng','Đăng link lên Facebook, Zalo, TikTok... Người xem đi qua trang quảng cáo 5 giây trước khi tới đích.', false],
            ['03','Nhận tiền','rút trong 24h','View hợp lệ cộng tiền vào ví theo CPM. Đạt 100.000đ là gửi yêu cầu rút về MoMo, ZaloPay hoặc PayPal.', false],
        ];
    @endphp
    <div class="wrap" style="position:relative;margin-top:60px;">
        <div class="grid-auto two-col" style="grid-template-columns:repeat(3,1fr);">
            @foreach($steps as $s)
                <div style="border:1px solid var(--line);border-radius:10px;padding:38px 34px;background:rgba(255,255,255,.012);{{ $s[4] ? 'border-bottom:2px solid var(--pk);' : '' }}">
                    <div style="display:flex;align-items:center;gap:18px;margin-bottom:48px;">
                        <span class="mono" style="font-size:30px;font-weight:500;color:{{ $s[4] ? 'var(--pk)' : 'var(--mut2)' }};">{{ $s[0] }}</span>
                        <span style="flex:1;height:1px;background:{{ $s[4] ? 'linear-gradient(90deg,var(--pk),transparent)' : 'var(--line)' }};"></span>
                    </div>
                    <h3 style="font-size:27px;font-weight:600;margin:0;letter-spacing:-.01em;">{{ $s[1] }}</h3>
                    <div style="color:var(--mut);font-size:15px;margin-top:6px;">{{ $s[2] }}</div>
                    <p style="color:var(--mut2);font-size:14px;margin-top:22px;line-height:1.65;">{{ $s[3] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════ LIVE STATS ══════════ --}}
<section style="padding:120px 0;position:relative;overflow:hidden;">
    <div class="wrap">
        <div style="border:1px solid var(--line);border-radius:28px;overflow:hidden;position:relative;background:#0A0A12;">
            <img src="{{ asset('images/fink/world.png') }}" alt="Mạng lưới chia sẻ link toàn cầu" width="1369" height="1369" loading="lazy" style="position:absolute;right:-40px;top:50%;transform:translateY(-50%);width:560px;max-width:54%;mix-blend-mode:screen;">
            <div style="position:absolute;inset:0;background:radial-gradient(circle at 85% 50%, rgba(103,232,249,.1), transparent 45%);"></div>
            <div style="position:relative;padding:60px 50px;">
                <div class="eyebrow" style="margin-bottom:20px;">Số liệu thật · cập nhật trực tiếp</div>
                <h2 class="display" style="font-size:clamp(30px,4.5vw,52px);max-width:560px;">Cộng đồng đang kiếm tiền<br><span class="serif grad">cùng LinkPay.</span></h2>
                @php
                    $live = [
                        [$compact($stats['total_clicks'] ?? 0),'Lượt click xử lý'],
                        [$compact($stats['total_users'] ?? 0),'Người dùng'],
                        [$compact($stats['total_paid'] ?? 0).'đ','Đã trả creator'],
                        [number_format($stats['active_links'] ?? 0),'Link đang chạy'],
                    ];
                @endphp
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:30px;margin-top:48px;max-width:640px;">
                    @foreach($live as $l)
                        <div>
                            <div style="font-size:38px;font-weight:700;letter-spacing:-.02em;">{{ $l[0] }}</div>
                            <div style="font-size:13px;color:var(--mut);margin-top:4px;">{{ $l[1] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════ PAYMENT ══════════ --}}
<section id="pay" style="padding:120px 0;background:linear-gradient(180deg,#0A0A12,#08080B);">
    <div class="wrap">
        <div style="max-width:680px;margin-bottom:60px;">
            <div class="eyebrow" style="margin-bottom:24px;">Rút tiền</div>
            <h2 class="display" style="font-size:clamp(34px,5vw,60px);">Ba kênh thanh toán.<br><span class="mut2">Không phí ẩn.</span></h2>
        </div>
        @php
            $pays = [
                ['MoMo','radial-gradient(circle at 30% 30%,#ff4dab,#ea27c2 55%,#a50064)','momo.svg','width:88px;height:88px;','Chuyển khoản qua số điện thoại MoMo. Tối thiểu 100.000đ.'],
                ['ZaloPay','linear-gradient(135deg,#00ABFC,#0068FF)','zalopay.svg','max-width:78%;max-height:58%;','Chuyển khoản qua tài khoản ZaloPay. Tối thiểu 100.000đ.'],
                ['PayPal','linear-gradient(135deg,#003087,#001F5C)','paypal.svg','max-width:72%;max-height:52%;filter:brightness(0) invert(1);','Chuyển USD qua email PayPal. Tối thiểu $4 (~100k).'],
            ];
        @endphp
        <div class="grid-auto" style="grid-template-columns:repeat(auto-fit,minmax(265px,1fr));">
            @foreach($pays as $p)
                <div class="card" style="padding:0;">
                    <div style="aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;background:{{ $p[1] }};">
                        <img src="{{ asset('images/payment/'.$p[2]) }}" alt="{{ $p[0] }}" loading="lazy" style="{{ $p[3] }}">
                    </div>
                    <div style="padding:24px;">
                        <h3 style="font-size:18px;font-weight:700;margin:0;">{{ $p[0] }}</h3>
                        <p style="color:var(--mut);font-size:14px;margin-top:8px;line-height:1.6;">{{ $p[4] }}</p>
                        <div style="margin-top:14px;font-size:12px;font-weight:700;color:var(--gr);">Duyệt trong 24h · Phí 0đ</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════ SECURITY ══════════ --}}
<section style="padding:120px 0;">
    <div class="wrap">
        <div style="max-width:680px;margin-bottom:60px;">
            <div class="eyebrow" style="margin-bottom:24px;">An toàn &amp; minh bạch</div>
            <h2 class="display" style="font-size:clamp(34px,5vw,60px);">Bảo vệ thu nhập<br><span class="serif" style="color:var(--cy);">của bạn.</span></h2>
        </div>
        @php
            $secs = [
                ['isolated.jpg','Chống tự click','Nhận diện self-click qua tài khoản + IP, không cộng tiền cho view gian lận.'],
                ['encrypted.jpg','Mật khẩu &amp; HTTPS','Toàn site chạy HTTPS, link có thể đặt mật khẩu bảo vệ nội dung.'],
                ['audit.jpg','Nhật ký đầy đủ','Mọi click được ghi lại: IP, thiết bị, thời gian — tra cứu minh bạch.'],
                ['permissions.jpg','Dedup 24h','Mỗi IP chỉ tính 1 view hợp lệ trong 24h, chặn spam refresh.'],
            ];
        @endphp
        <div class="grid-auto" style="grid-template-columns:repeat(auto-fit,minmax(245px,1fr));">
            @foreach($secs as $s)
                <div class="card" style="padding:0;">
                    <div style="height:150px;position:relative;">
                        <img src="{{ asset('images/fink/'.$s[0]) }}" alt="{{ strip_tags($s[1]) }}" width="840" height="840" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                        <div style="position:absolute;inset:0;background:linear-gradient(180deg,transparent,#0A0A12);"></div>
                    </div>
                    <div style="padding:22px;">
                        <h3 style="font-size:17px;font-weight:700;margin:0;">{!! $s[1] !!}</h3>
                        <p style="color:var(--mut);font-size:13px;margin-top:8px;line-height:1.6;">{{ $s[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════ STATEMENT ══════════ --}}
<section style="padding:90px 0;">
    <div class="wrap" style="max-width:880px;">
        <div class="card" style="padding:56px;text-align:center;background:radial-gradient(circle at 50% 0%, rgba(167,139,250,.14), transparent 60%);">
            <div class="serif" style="font-size:clamp(24px,3.2vw,38px);line-height:1.3;">
                Mỗi liên kết bạn chia sẻ là một <span style="color:var(--pk);">nguồn thu nhập</span>. View hợp lệ cộng tiền vào ví, rút trong 24h — minh bạch, không phí ẩn.
            </div>
            <div style="margin-top:40px;display:flex;justify-content:center;gap:56px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:34px;font-weight:700;color:var(--gr);letter-spacing:-.02em;">{{ $compact($stats['total_paid'] ?? 0) }}đ</div>
                    <div style="font-size:13px;color:var(--mut);margin-top:4px;">đã trả cho người dùng</div>
                </div>
                <div>
                    <div style="font-size:34px;font-weight:700;letter-spacing:-.02em;" class="grad">{{ $compact($stats['total_users'] ?? 0) }}</div>
                    <div style="font-size:13px;color:var(--mut);margin-top:4px;">người dùng đang kiếm tiền</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════ FAQ ══════════ --}}
<section style="padding:90px 0;">
    <div class="wrap" style="max-width:780px;">
        <div style="margin-bottom:44px;">
            <div class="eyebrow" style="margin-bottom:24px;">Câu hỏi thường gặp</div>
            <h2 class="display" style="font-size:clamp(32px,4.5vw,54px);">Bạn còn <span class="serif grad">băn khoăn?</span></h2>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($faqs as $faq)
                <details class="card" style="padding:0;">
                    <summary style="display:flex;align-items:center;justify-content:space-between;padding:22px 24px;font-weight:600;">
                        <span>{{ $faq['q'] }}</span>
                        <span class="plus" style="color:var(--vi);flex-shrink:0;margin-left:16px;transition:.25s;font-size:20px;">＋</span>
                    </summary>
                    <div style="padding:0 24px 22px;color:var(--mut);font-size:14px;line-height:1.7;">{{ $faq['a'] }}</div>
                </details>
            @endforeach
        </div>
        <div style="margin-top:28px;text-align:center;">
            <a href="{{ route('faq') }}" style="color:var(--cy);font-weight:700;font-size:14px;">Xem tất cả câu hỏi →</a>
        </div>
    </div>
</section>

{{-- ══════════ CTA (two trees) ══════════ --}}
<section style="padding:90px 0;">
    <div class="wrap">
        <div style="position:relative;border-radius:30px;overflow:hidden;border:1px solid var(--line);min-height:480px;display:flex;align-items:center;justify-content:center;text-align:center;">
            <img src="{{ asset('images/scenery/bridge.webp') }}" alt="Hai liên kết kết nối nhau" width="1600" height="1067" loading="lazy" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
            <div style="position:absolute;inset:0;background:radial-gradient(circle at 50% 62%, rgba(8,8,11,.35), rgba(8,8,11,.85));"></div>
            <div style="position:relative;padding:64px 24px;">
                <div class="pill" style="margin-bottom:24px;background:rgba(255,255,255,.06);">✨ Mỗi liên kết là một cây cầu kiếm tiền</div>
                <h2 class="display" style="font-size:clamp(32px,5.5vw,60px);max-width:740px;margin:0 auto;text-shadow:0 2px 30px rgba(0,0,0,.6);">
                    Sẵn sàng biến mỗi click <span class="serif" style="color:var(--pk);">thành tiền?</span>
                </h2>
                <p style="font-size:18px;color:#D6D8DF;margin:18px auto 0;max-width:500px;">Đăng ký miễn phí, tạo link đầu tiên và bắt đầu kiếm tiền ngay hôm nay.</p>
                <div style="margin-top:34px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <a href="{{ route('register') }}" class="btn btn-grad" style="padding:15px 30px;">Bắt đầu miễn phí →</a>
                    <a href="{{ route('faq') }}" class="btn btn-ghost" style="padding:15px 30px;">Tìm hiểu thêm</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════ LANDSCAPE BAND ══════════ --}}
<div style="position:relative;width:100%;overflow:hidden;background:#050507;">
    <img src="{{ asset('images/fink/landscape.png') }}" alt="" width="2720" height="811" loading="lazy" style="width:100%;height:auto;display:block;object-fit:cover;">
    <div style="position:absolute;left:0;right:0;top:0;height:35%;background:linear-gradient(180deg,#08080B,transparent);"></div>
    <div style="position:absolute;left:0;right:0;bottom:0;height:45%;background:linear-gradient(180deg,transparent,#050507);"></div>
</div>

{{-- ══════════ FOOTER ══════════ --}}
<footer style="background:#050507;padding:24px 0 40px;">
    <div class="wrap two-col" style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:40px;">
        <div>
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:9px;font-weight:700;font-size:18px;">
                <span style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,var(--cy),var(--vi));display:inline-flex;align-items:center;justify-content:center;color:#0B0B14;font-weight:800;font-size:12px;">LP</span>
                Link<span class="grad">Pay</span><span style="color:var(--mut);font-size:11px;align-self:flex-start;">™</span>
            </a>
            <p style="color:var(--mut);font-size:14px;margin-top:14px;max-width:260px;line-height:1.6;">Rút gọn link kèm quảng cáo. Mỗi view hợp lệ là tiền vào ví của bạn.</p>
            <div style="display:flex;gap:18px;margin-top:20px;font-size:14px;color:var(--mut);">
                <a href="https://github.com/DucMinhNe" target="_blank" style="display:inline-flex;align-items:center;gap:6px;">GitHub</a>
            </div>
        </div>
        @php
            $fcols = [
                ['Sản phẩm', [['Tính năng','#features'],['Cách hoạt động','#how'],['Thanh toán','#pay'],['FAQ',route('faq')],['Bảng xếp hạng',route('leaderboard')]]],
                ['Tài khoản', [['Đăng nhập',route('login')],['Đăng ký',route('register')],['Trợ giúp',route('faq')]]],
                ['Công ty', [['Giới thiệu',route('faq')],['Liên hệ',route('contact')]]],
                ['Pháp lý', [['Điều khoản',route('terms')],['Bảo mật',route('privacy')],['Cookie',route('privacy')]]],
            ];
        @endphp
        @foreach($fcols as $col)
            <div>
                <div style="font-weight:700;font-size:13px;margin-bottom:14px;">{{ $col[0] }}</div>
                <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;color:var(--mut);">
                    @foreach($col[1] as $lnk)<a href="{{ $lnk[1] }}">{{ $lnk[0] }}</a>@endforeach
                </div>
            </div>
        @endforeach
    </div>
    <div class="wrap" style="margin-top:48px;padding-top:24px;border-top:1px solid var(--line);display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:13px;color:var(--mut);">
        <span>© {{ date('Y') }} LinkPay. Bảo lưu mọi quyền.</span>
        <span>🇻🇳 Tiếng Việt</span>
    </div>
</footer>

</body>
</html>
