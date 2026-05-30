<x-app-layout :title="'LinkPay Premium'">
    <x-slot name="header">Premium</x-slot>

    @php $user = auth()->user(); $isPremium = $user->isPremium(); @endphp

    <div class="max-w-[1000px] mx-auto space-y-7">

        {{-- Hero --}}
        <div class="lp-glass-dark !p-8">
            <div class="blob" style="width:260px;height:260px;background:#F59E0B;left:-40px;top:-80px;"></div>
            <div class="blob" style="width:240px;height:240px;background:#EC4899;right:-30px;bottom:-80px;"></div>
            <div class="relative flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex-1">
                    <span class="lp-tag lp-tag-amber">✦ LinkPay Premium</span>
                    <h1 class="text-white font-black mt-3" style="font-size:clamp(28px,4vw,44px);line-height:1.05;">
                        Tắt quảng cáo. <span class="lp-grad-text-warm">Kiếm nhiều hơn.</span>
                    </h1>
                    <p class="text-white/60 mt-3 max-w-[560px]">Bỏ qua trang chờ quảng cáo, rút tiền ưu tiên trong 6h, tỉ lệ /1000 view cao hơn 30%, và huy hiệu PRO nổi bật.</p>
                </div>
                @if($isPremium)
                    <div class="text-center bg-white/10 border border-white/15 rounded-2xl p-5">
                        <div class="text-white/60 text-xs uppercase tracking-wider">Trạng thái</div>
                        <div class="text-white text-2xl font-black mt-1">PRO ✓</div>
                        <div class="text-white/70 text-sm mt-1">Hết hạn {{ $user->premium_until?->format('d/m/Y') }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Feature grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['heroicon-o-no-symbol','lp-ic-pink','Không quảng cáo','Link của bạn redirect thẳng, không trang chờ.'],
                ['heroicon-o-bolt','lp-ic-amber','Rút tiền ưu tiên','Duyệt trong 6h thay vì 24h.'],
                ['heroicon-o-arrow-trending-up','lp-ic-green','Tỉ lệ cao hơn 30%','Mỗi 1000 view hợp lệ trả nhiều hơn.'],
                ['heroicon-o-sparkles','lp-ic-violet','Huy hiệu PRO','Nổi bật trên bảng xếp hạng & hồ sơ.'],
            ] as [$icon,$ic,$t,$d])
                <div class="card-icon-feature lp-kpi !p-5">
                    <div class="lp-ic {{ $ic }}"><x-dynamic-component :component="$icon" class="w-5 h-5"/></div>
                    <div class="type-body-md-bold text-ink-deep mt-4">{{ $t }}</div>
                    <div class="type-body-sm text-slate mt-1">{{ $d }}</div>
                </div>
            @endforeach
        </div>

        {{-- Pricing --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="card-feature !p-7">
                <div class="type-caption-bold uppercase tracking-wider text-stone">Hàng tháng</div>
                <div class="flex items-end gap-1 mt-2">
                    <span class="type-display-md text-ink-deep">49.000</span><span class="type-subtitle-md text-slate mb-1">đ/tháng</span>
                </div>
                <form method="POST" action="{{ route('premium.upgrade') }}" class="mt-5">
                    @csrf <input type="hidden" name="plan" value="month">
                    <button class="btn btn-primary w-full justify-center">
                        {{ $isPremium ? 'Gia hạn 1 tháng' : 'Nâng cấp ngay' }}
                    </button>
                </form>
            </div>
            <div class="card-feature lp-kpi lp-accent-warm !p-7 relative">
                <span class="lp-tag lp-tag-amber" style="position:absolute;top:18px;right:18px;">Tiết kiệm 30%</span>
                <div class="type-caption-bold uppercase tracking-wider text-stone">Hàng năm</div>
                <div class="flex items-end gap-1 mt-2">
                    <span class="type-display-md text-ink-deep">410.000</span><span class="type-subtitle-md text-slate mb-1">đ/năm</span>
                </div>
                <form method="POST" action="{{ route('premium.upgrade') }}" class="mt-5">
                    @csrf <input type="hidden" name="plan" value="year">
                    <button class="lp-btn-grad warm w-full justify-center">
                        {{ $isPremium ? 'Gia hạn 1 năm' : 'Nâng cấp tiết kiệm' }}
                    </button>
                </form>
            </div>
        </div>
        <p class="type-caption text-stone text-center">Đây là môi trường demo — nâng cấp được kích hoạt ngay, không thu phí thật.</p>
    </div>
</x-app-layout>
