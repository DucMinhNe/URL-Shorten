@props(['size' => 'md', 'variant' => 'dark'])

@php
    $textColor = $variant === 'light' ? 'text-on-dark' : 'text-ink-deep';
    $dot = $variant === 'light' ? '#FFD60A' : '#696CFF';
    $box = $variant === 'light' ? '#FFFFFF' : '#22303E';
    $h = $size === 'lg' ? 28 : 22;
@endphp

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-2 {$textColor}"]) }}>
    <svg width="{{ $h }}" height="{{ $h }}" viewBox="0 0 32 32" fill="none">
        <rect x="2" y="2" width="28" height="28" rx="8" fill="{{ $box }}"/>
        <path d="M11 18.5 L21 13.5" stroke="{{ $dot }}" stroke-width="2.5" stroke-linecap="round"/>
        <circle cx="11" cy="18.5" r="3" fill="{{ $dot }}"/>
        <circle cx="21" cy="13.5" r="3" fill="{{ $dot }}"/>
    </svg>
    <span class="type-link-md tracking-tight font-bold">LinkPay</span>
</a>
