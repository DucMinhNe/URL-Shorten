<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $code }} · {{ $title }} · LinkPay</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400..700&family=Instrument+Serif:ital@1&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    *{box-sizing:border-box;margin:0;}
    body{background:#08080B;color:#F2F2F5;font-family:'Instrument Sans',system-ui,sans-serif;min-height:100vh;
        display:flex;align-items:center;justify-content:center;padding:24px;overflow:hidden;position:relative;-webkit-font-smoothing:antialiased;}
    .glow{position:fixed;border-radius:9999px;filter:blur(130px);pointer-events:none;z-index:0;}
    .wrap{position:relative;z-index:1;text-align:center;max-width:520px;}
    .brand{display:inline-flex;align-items:center;gap:9px;font-weight:700;font-size:17px;margin-bottom:36px;letter-spacing:-.01em;}
    .lp{width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,#67E8F9,#A78BFA);display:inline-flex;align-items:center;justify-content:center;color:#0B0B14;font-weight:800;font-size:12px;}
    .grad{background:linear-gradient(110deg,#67E8F9,#A78BFA 50%,#ECA8D6);-webkit-background-clip:text;background-clip:text;color:transparent;}
    .code{font-size:clamp(90px,18vw,160px);font-weight:700;line-height:.9;letter-spacing:-.04em;}
    .serif{font-family:'Instrument Serif',serif;font-style:italic;font-weight:400;}
    h1{font-size:clamp(22px,4vw,30px);font-weight:700;margin:14px 0 10px;}
    p{color:#9AA0AE;font-size:15px;line-height:1.6;margin-bottom:28px;}
    .btn{display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:14px;border-radius:999px;padding:13px 26px;
        background:linear-gradient(110deg,#A5F3FC,#C4B5FD 55%,#FBCFE8);color:#0B0B14;transition:.25s;text-decoration:none;}
    .btn:hover{transform:translateY(-2px);box-shadow:0 16px 44px -8px rgba(167,139,250,.6);}
    .ghost{color:#9AA0AE;font-size:13px;text-decoration:none;margin-left:18px;}
    .ghost:hover{color:#F2F2F5;}
    .mono{font-family:'JetBrains Mono',monospace;}
</style>
</head>
<body>
<div class="glow" style="width:480px;height:480px;background:#A78BFA;opacity:.18;top:-140px;left:-100px;"></div>
<div class="glow" style="width:420px;height:420px;background:#67E8F9;opacity:.12;bottom:-160px;right:-80px;"></div>
<div class="wrap">
    <a href="{{ url('/') }}" class="brand">
        <span class="lp">LP</span> Link<span class="grad">Pay</span>
    </a>
    <div class="code grad">{{ $code }}</div>
    <h1>{{ $title }}</h1>
    <p>{{ $msg }}</p>
    <div>
        <a href="{{ url('/') }}" class="btn">← Về trang chủ</a>
        <a href="{{ route('faq') }}" class="ghost">Xem FAQ</a>
    </div>
</div>
</body>
</html>
