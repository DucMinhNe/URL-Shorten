@if($ad->type === 'banner_image')
    @if($ad->target_url)
        <a href="{{ $ad->target_url }}" target="_blank" rel="noopener" data-ad-id="{{ $ad->id }}">
            <img src="{{ $ad->content }}" alt="{{ $ad->name }}" class="max-w-full h-auto">
        </a>
    @else
        <img src="{{ $ad->content }}" alt="{{ $ad->name }}" class="max-w-full h-auto">
    @endif
@elseif($ad->type === 'html')
    <div class="ad-html">{!! $ad->content !!}</div>
@else
    <iframe src="{{ $ad->content }}" class="border-0" width="728" height="90"></iframe>
@endif
