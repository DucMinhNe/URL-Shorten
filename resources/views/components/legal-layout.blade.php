@props(['title', 'active' => '', 'updated' => '30/05/2026'])

<x-guest-layout :title="$title.' · LinkPay'">
<x-public-nav :active="$active"/>

<section style="background:linear-gradient(180deg,#0B0B14 0%,#15172A 100%);">
    <div class="max-w-[820px] mx-auto px-6 py-16 text-center">
        <span class="lp-tag lp-tag-cyan">LinkPay</span>
        <h1 class="text-white font-black mt-4" style="font-size:clamp(30px,5vw,46px);line-height:1.05;">{{ $title }}</h1>
        <p class="text-white/50 mono mt-3" style="font-size:13px;">Cập nhật lần cuối: {{ $updated }}</p>
    </div>
</section>

<section class="bg-app-soft py-14">
    <div class="max-w-[820px] mx-auto px-6">
        <article class="bg-white rounded-3xl border border-hairline-soft shadow-sm p-8 sm:p-10"
                 style="line-height:1.7;">
            {{ $slot }}
        </article>
        <p class="text-center text-slate mt-8 type-body-sm">
            Câu hỏi khác? Xem <a href="{{ route('faq') }}" class="text-primary font-bold">FAQ</a>
            hoặc <a href="{{ route('contact') }}" class="text-primary font-bold">liên hệ</a>.
        </p>
    </div>
</section>

<x-public-footer/>
</x-guest-layout>
