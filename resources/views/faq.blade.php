<x-guest-layout :title="'Câu hỏi thường gặp · LinkPay'"
    description="Giải đáp về cách kiếm tiền, rút tiền MoMo/ZaloPay/PayPal, view hợp lệ và quản lý liên kết trên LinkPay."
    seoType="FAQPage" :faqs="$faqsFlat">
<x-public-nav active="faq"/>

{{-- Hero --}}
<section class="relative overflow-hidden" style="background: linear-gradient(180deg, #F0F4FB 0%, #FAFBFE 100%);">
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-30 blur-3xl" style="background: radial-gradient(circle, #BFDBFE 0%, transparent 70%);"></div>
    <div class="relative max-w-[760px] mx-auto px-6 pt-16 pb-12 text-center">
        <div class="section-label justify-center mb-4"><span>Trung tâm trợ giúp</span></div>
        <h1 class="type-display-lg text-ink-deep">Câu hỏi <span class="font-light italic text-slate">thường gặp.</span></h1>
        <p class="type-subtitle-md text-charcoal mt-4 max-w-[560px] mx-auto">
            Mọi điều bạn cần biết về cách kiếm tiền, rút tiền và quản lý liên kết. Không tìm thấy câu trả lời?
            <a href="{{ route('register') }}" class="text-ink-deep font-bold underline underline-offset-2">Tạo tài khoản</a> và hỏi support.
        </p>
    </div>
</section>

{{-- Groups --}}
<section class="py-16 lg:py-20">
    <div class="max-w-[760px] mx-auto px-6 space-y-12">
        @foreach($groups as $group)
            <div>
                <div class="flex items-center gap-3 mb-5">
                    <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-primary-soft text-primary-deep">
                        <x-dynamic-component :component="$group['icon']" class="w-5 h-5"/>
                    </div>
                    <h2 class="type-heading-sm text-ink-deep">{{ $group['title'] }}</h2>
                </div>

                <div class="space-y-3">
                    @foreach($group['items'] as $item)
                        <details class="group card-icon-feature !p-0">
                            <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                                <span class="type-subtitle-lg text-ink-deep pr-4">{{ $item['q'] }}</span>
                                <span class="btn-icon-ghost flex-shrink-0 group-open:rotate-180 transition-transform">
                                    <x-heroicon-o-chevron-down class="w-5 h-5"/>
                                </span>
                            </summary>
                            <div class="px-6 pb-6 type-body-md text-slate -mt-2">{{ $item['a'] }}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- CTA --}}
        <div class="card-promo-dark !p-10 text-center">
            <h2 class="type-heading-md text-on-dark font-light italic">Sẵn sàng biến mỗi click thành tiền?</h2>
            <p class="type-body-md text-stone mt-3 max-w-[440px] mx-auto">Đăng ký miễn phí, tạo link đầu tiên và bắt đầu kiếm tiền ngay hôm nay.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-buy">Bắt đầu miễn phí <x-heroicon-m-arrow-right class="w-4 h-4"/></a>
                <a href="{{ route('home') }}" class="btn btn-secondary !border-stone/40 !text-on-dark hover:!bg-white/10">Về trang chủ</a>
            </div>
        </div>
    </div>
</section>

<x-public-footer/>
</x-guest-layout>
