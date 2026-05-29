@props([
    'title' => 'LinkPay — Mỗi click là tiền',
    'description' => 'Rút gọn link kèm quảng cáo và kiếm tiền theo mỗi lượt view hợp lệ. Rút về MoMo, ZaloPay hoặc PayPal — duyệt trong 24h, không phí ẩn.',
    'image' => null,
    'seoType' => 'website',
    'faqs' => [],
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }}</title>

        <x-seo :title="$title" :description="$description" :image="$image" :type="$seoType" :faqs="$faqs"/>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="bg-canvas text-ink-deep min-h-screen">
        {{ $slot }}
    </body>
</html>
