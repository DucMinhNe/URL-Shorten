@props([
    'title' => 'LinkPay — Mỗi click là tiền',
    'description' => 'Rút gọn link kèm quảng cáo và kiếm tiền theo mỗi lượt view hợp lệ. Rút về MoMo, ZaloPay hoặc PayPal — duyệt trong 24h, không phí ẩn.',
    'image' => null,
    'type' => 'website',
    'faqs' => [],
])

@php
    $appName = config('app.name', 'LinkPay');
    $appUrl = rtrim(config('app.url'), '/');
    $canonical = $appUrl.'/'.ltrim(request()->path() === '/' ? '' : request()->path(), '/');
    $ogImage = $image ?: $appUrl.'/og-image.png';
@endphp

<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="theme-color" content="#0064E0">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $type === 'FAQPage' ? 'website' : $type }}">
<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="vi_VN">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">

{{-- JSON-LD: Organization + WebSite --}}
<script type="application/ld+json">
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            'name' => $appName,
            'url' => $appUrl,
            'logo' => $appUrl.'/icon-512.png',
        ],
        [
            '@type' => 'WebSite',
            'name' => $appName,
            'url' => $appUrl,
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

@if($type === 'FAQPage' && count($faqs))
<script type="application/ld+json">
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
