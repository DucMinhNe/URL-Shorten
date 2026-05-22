<x-guest-layout :title="'Liên kết không khả dụng · LinkPay'">
<div class="min-h-screen flex flex-col bg-surface-soft">
    <header class="bg-canvas border-b border-hairline-soft">
        <div class="max-w-[1400px] mx-auto px-6 h-14 flex items-center justify-between">
            <x-brand size="md"/>
            <a href="{{ route('home') }}" class="type-body-sm text-slate hover:text-ink-deep">Về trang chủ →</a>
        </div>
    </header>

    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[480px] text-center">
            <div class="w-32 h-32 mx-auto mb-8 relative">
                <div class="absolute inset-0 rounded-full bg-[color:var(--color-critical-soft)]"></div>
                <div class="absolute inset-0 flex items-center justify-center text-critical">
                    <x-heroicon-o-link-slash class="w-16 h-16"/>
                </div>
            </div>

            <div class="section-label justify-center mb-3"><span>Liên kết không khả dụng</span></div>
            <h1 class="type-display-lg text-ink-deep">Link này không còn<br><span class="font-light italic text-slate">hoạt động.</span></h1>
            <p class="type-subtitle-md text-charcoal mt-4 max-w-[400px] mx-auto">
                Có thể link đã bị xoá, vô hiệu, hoặc bị admin chặn vì vi phạm chính sách.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <x-heroicon-m-arrow-left class="w-4 h-4"/>
                    Về trang chủ
                </a>
                <a href="#" class="btn btn-ghost">
                    <x-heroicon-o-flag class="w-4 h-4"/>
                    Báo cáo
                </a>
            </div>

            <p class="mt-6 type-caption text-stone">
                Bạn nghĩ đây là lỗi? <a href="#" class="text-ink-deep font-bold underline">Liên hệ support</a>
            </p>
        </div>
    </div>
</div>
</x-guest-layout>
