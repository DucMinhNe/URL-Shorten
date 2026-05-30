@props(['active' => 'home'])

<div class="sticky top-0 z-40" x-data="{open:false}">
    {{-- Promo banner --}}
    <div class="bg-ink-deep text-on-dark">
        <div class="max-w-[1280px] mx-auto px-6 py-3 flex items-center justify-center gap-3 type-body-sm-bold">
            <x-heroicon-s-sparkles class="w-4 h-4 text-[color:var(--color-warning)]"/>
            <span>Giới thiệu bạn bè — nhận thêm <span class="text-[color:var(--color-warning)]">50% rate</span> cho lượt click đầu</span>
            <a href="{{ route('faq') }}" class="underline underline-offset-4 hover:text-[color:var(--color-warning)] transition-colors">Xem chi tiết →</a>
        </div>
    </div>

    {{-- Main nav --}}
    <header class="bg-canvas border-b border-hairline-soft border-b-[1px]">
        <div class="max-w-[1280px] mx-auto px-6 h-16 flex items-center justify-between">
            <x-brand size="md"/>

            <nav class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" class="pill-tab {{ $active === 'home' ? 'active' : '' }}">Trang chủ</a>
                <a href="{{ route('home') }}#how" class="pill-tab {{ $active === 'how' ? 'active' : '' }}">Cách hoạt động</a>
                <a href="{{ route('home') }}#pay" class="pill-tab {{ $active === 'pay' ? 'active' : '' }}">Bảng giá</a>
                <a href="{{ route('faq') }}" class="pill-tab {{ $active === 'faq' ? 'active' : '' }}">FAQ</a>
                <a href="{{ route('leaderboard') }}" class="pill-tab {{ $active === 'leaderboard' ? 'active' : '' }}">Bảng xếp hạng</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full text-sm font-bold flex items-center gap-2 transition-all hover:shadow-md hover:-translate-y-0.5"
                       style="background: linear-gradient(135deg, #FF4D6D 0%, #E11D48 100%); color: white; box-shadow: 0 4px 12px -2px rgba(225, 29, 72, 0.4);">
                        Vào tổng quan
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold transition-colors" style="color: #475569;">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full text-sm font-bold flex items-center gap-2 transition-all hover:shadow-md hover:-translate-y-0.5"
                       style="background: linear-gradient(135deg, #FF4D6D 0%, #E11D48 100%); color: white; box-shadow: 0 4px 12px -2px rgba(225, 29, 72, 0.4);">
                        Bắt đầu
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                @endauth

                <button @click="open=!open" class="btn-icon-circ lg:hidden" aria-label="Menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5"/>
                </button>
            </div>
        </div>
    </header>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak class="lg:hidden border-b border-hairline-soft bg-canvas px-6 py-4 flex flex-col gap-1">
        <a href="{{ route('home') }}" class="pill-tab {{ $active === 'home' ? 'active' : '' }}">Trang chủ</a>
        <a href="{{ route('home') }}#how" class="pill-tab {{ $active === 'how' ? 'active' : '' }}">Cách hoạt động</a>
        <a href="{{ route('home') }}#pay" class="pill-tab {{ $active === 'pay' ? 'active' : '' }}">Bảng giá</a>
        <a href="{{ route('leaderboard') }}" class="pill-tab {{ $active === 'leaderboard' ? 'active' : '' }}">Bảng xếp hạng</a>
        <a href="{{ route('faq') }}" class="pill-tab {{ $active === 'faq' ? 'active' : '' }}">FAQ</a>
        <a href="{{ route('login') }}" class="pill-tab">Đăng nhập</a>
        <a href="{{ route('register') }}" class="pill-tab">Bắt đầu</a>
    </div>
</div>
