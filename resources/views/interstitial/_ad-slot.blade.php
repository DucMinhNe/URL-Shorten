@if($ad->type === 'banner_image')
    @if($ad->target_url)
        <a href="{{ $ad->target_url }}" target="_blank" rel="noopener" data-ad-id="{{ $ad->id }}" class="block absolute inset-0">
            <img src="{{ $ad->content }}" alt="{{ $ad->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
            <div class="absolute bottom-4 left-4 right-4 text-white pointer-events-none">
                <div class="text-xs font-bold uppercase tracking-wider text-white/80">{{ $ad->name }}</div>
                <div class="text-sm font-semibold mt-0.5 flex items-center gap-1.5">
                    Tìm hiểu thêm
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"/></svg>
                </div>
            </div>
        </a>
    @else
        <img src="{{ $ad->content }}" alt="{{ $ad->name }}" class="absolute inset-0 w-full h-full object-cover">
    @endif
@elseif($ad->type === 'html')
    <div class="absolute inset-0 overflow-hidden">{!! $ad->content !!}</div>
@else
    <iframe src="{{ $ad->content }}" class="border-0 absolute inset-0 w-full h-full"></iframe>
@endif
