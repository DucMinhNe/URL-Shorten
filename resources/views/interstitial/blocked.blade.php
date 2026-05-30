<x-guest-layout :title="'Liên kết không khả dụng · LinkPay'">
<div class="min-h-screen flex flex-col bg-surface-soft">
    <header class="bg-canvas border-b border-hairline-soft">
        <div class="max-w-[1400px] mx-auto px-6 h-14 flex items-center justify-between">
            <x-brand size="md"/>
            <a href="{{ route('home') }}" class="type-body-sm text-slate hover:text-ink-deep">Về trang chủ →</a>
        </div>
    </header>

    @php
        $reason = $reason ?? 'blocked';
        $copy = [
            'expired' => [
                'label' => 'Liên kết đã hết hạn',
                'heading' => 'Link này đã <span class="font-light italic text-slate">hết hạn.</span>',
                'desc' => 'Người tạo đã đặt ngày hết hạn cho liên kết này và thời hạn đã trôi qua.',
            ],
            'limit_reached' => [
                'label' => 'Liên kết đã đạt giới hạn',
                'heading' => 'Link đã đạt <span class="font-light italic text-slate">giới hạn click.</span>',
                'desc' => 'Người tạo đã giới hạn số lượt truy cập và liên kết này đã đạt mức tối đa.',
            ],
            'blocked' => [
                'label' => 'Liên kết không khả dụng',
                'heading' => 'Link này không còn<br><span class="font-light italic text-slate">hoạt động.</span>',
                'desc' => 'Có thể link đã bị xoá, vô hiệu, hoặc bị admin chặn vì vi phạm chính sách.',
            ],
        ][$reason] ?? null;
        $copy ??= [
            'label' => 'Liên kết không khả dụng',
            'heading' => 'Link này không còn<br><span class="font-light italic text-slate">hoạt động.</span>',
            'desc' => 'Liên kết này hiện không thể truy cập.',
        ];
        $icon = $reason === 'expired' ? 'heroicon-o-clock' : ($reason === 'limit_reached' ? 'heroicon-o-no-symbol' : 'heroicon-o-link-slash');
    @endphp
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[480px] text-center">
            <div class="w-32 h-32 mx-auto mb-8 relative">
                <div class="absolute inset-0 rounded-full bg-[color:var(--color-critical-soft)]"></div>
                <div class="absolute inset-0 flex items-center justify-center text-critical">
                    <x-dynamic-component :component="$icon" class="w-16 h-16"/>
                </div>
            </div>

            <div class="section-label justify-center mb-3"><span>{{ $copy['label'] }}</span></div>
            <h1 class="type-display-lg text-ink-deep">{!! $copy['heading'] !!}</h1>
            <p class="type-subtitle-md text-charcoal mt-4 max-w-[400px] mx-auto">
                {{ $copy['desc'] }}
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <x-heroicon-m-arrow-left class="w-4 h-4"/>
                    Về trang chủ
                </a>
            </div>

            <p class="mt-6 type-caption text-stone">
                Bạn nghĩ đây là lỗi? <a href="mailto:support@mess.io.vn" class="text-ink-deep font-bold underline">Liên hệ support</a>
            </p>
        </div>
    </div>
</div>
</x-guest-layout>
