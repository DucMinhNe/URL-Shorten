@props([
    'items' => [],
    'empty' => 'Chưa có dữ liệu.',
    'mono' => false,
])

@php
    $total = array_sum($items);
@endphp

@if($total === 0)
    <p class="type-body-sm text-stone py-8 text-center">{{ $empty }}</p>
@else
    <div class="space-y-3">
        @foreach($items as $label => $count)
            @php $pct = round($count / $total * 100, 1); @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="type-body-sm-bold text-charcoal truncate pr-2 {{ $mono ? 'font-mono' : '' }}">{{ $label }}</span>
                    <span class="type-caption-bold text-stone flex-shrink-0">{{ number_format($count) }} · {{ $pct }}%</span>
                </div>
                <div class="h-2 rounded-full bg-surface-soft overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary-deep" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
@endif
