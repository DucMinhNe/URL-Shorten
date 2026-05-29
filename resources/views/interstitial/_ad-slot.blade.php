@php
    // Decode ad metadata (factory stores JSON in content field for banner_image type with overlay)
    $meta = null;
    if ($ad->type === 'banner_image') {
        $decoded = json_decode($ad->content ?? '', true);
        if (is_array($decoded) && isset($decoded['image'])) {
            $meta = $decoded;
        }
    }
@endphp

@if($meta)
    {{-- Real ad creative: photo background + brand overlay + Vietnamese copy + CTA --}}
    @php
        $imgPath = ltrim($meta['image'], '/');
        $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $imgPath);
    @endphp
    <a href="{{ $ad->target_url ?? '#' }}" target="_blank" rel="noopener" class="block absolute inset-0 group">
        {{-- Background photo (WebP + JPG fallback) --}}
        <picture class="absolute inset-0 block w-full h-full">
            <source srcset="{{ asset($webpPath) }}" type="image/webp">
            <img src="{{ asset($imgPath) }}" alt="{{ $meta['brand'] }}" decoding="async" class="w-full h-full object-cover">
        </picture>

        {{-- Brand-colored gradient overlay (left side strong, fade right) --}}
        <div class="absolute inset-0" style="background: linear-gradient(90deg, {{ $meta['color'] }}EE 0%, {{ $meta['color'] }}AA 40%, {{ $meta['color'] }}55 70%, transparent 100%);"></div>

        {{-- Content --}}
        <div class="absolute inset-0 flex flex-col justify-center px-6 lg:px-10 text-white pointer-events-none">
            {{-- Brand badge --}}
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/95 text-xs font-bold w-fit mb-3" style="color: {{ $meta['color'] }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $meta['color'] }};"></span>
                {{ $meta['brand'] }}
            </div>
            {{-- Headline --}}
            <div class="text-2xl lg:text-3xl xl:text-4xl font-black tracking-tight leading-none">{{ $meta['headline'] }}</div>
            {{-- Subhead --}}
            <div class="text-xs lg:text-sm font-medium mt-2 opacity-95 max-w-[80%]">{{ $meta['sub'] }}</div>
            {{-- CTA chip --}}
            <div class="mt-4 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white text-xs font-bold w-fit group-hover:gap-2 transition-all" style="color: {{ $meta['color'] }};">
                {{ $meta['cta'] }}
                <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"/>
                </svg>
            </div>
        </div>
    </a>
@elseif($ad->type === 'banner_image')
    {{-- Fallback: simple banner image (no overlay metadata) --}}
    @if($ad->target_url)
        <a href="{{ $ad->target_url }}" target="_blank" rel="noopener" class="block absolute inset-0">
            <img src="{{ $ad->content }}" alt="{{ $ad->name }}" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover">
        </a>
    @else
        <img src="{{ $ad->content }}" alt="{{ $ad->name }}" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover">
    @endif
@elseif($ad->type === 'html')
    <div class="absolute inset-0 overflow-hidden">{!! $ad->content !!}</div>
@else
    <iframe src="{{ $ad->content }}" class="border-0 absolute inset-0 w-full h-full"></iframe>
@endif
