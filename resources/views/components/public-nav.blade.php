@props(['active' => 'home'])

<div class="sticky top-0 z-40">
    {{-- Promo banner --}}
    <div class="bg-ink-deep text-on-dark">
        <div class="max-w-[1280px] mx-auto px-6 py-3 flex items-center justify-center gap-3 type-body-sm-bold">
            <x-heroicon-s-sparkles class="w-4 h-4 text-[color:var(--color-warning)]"/>
            <span>Khuyến mãi đầu tháng — tăng <span class="text-[color:var(--color-warning)]">50% rate</span> đến 31/05</span>
            <a href="#" class="underline underline-offset-4 hover:text-[color:var(--color-warning)] transition-colors">Xem chi tiết →</a>
        </div>
    </div>

    {{-- Main nav --}}
    <header class="bg-canvas border-b border-hairline-soft border-b-[1px]">
        <div class="max-w-[1280px] mx-auto px-6 h-16 flex items-center justify-between">
            <x-brand size="md"/>

            <nav class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" class="pill-tab {{ $active === 'home' ? 'active' : '' }}">Trang chủ</a>
                <a href="#how" class="pill-tab {{ $active === 'how' ? 'active' : '' }}">Cách hoạt động</a>
                <a href="#pricing" class="pill-tab {{ $active === 'pricing' ? 'active' : '' }}">Bảng giá</a>
                <a href="#faq" class="pill-tab {{ $active === 'faq' ? 'active' : '' }}">FAQ</a>
            </nav>

            <div class="flex items-center gap-2">
                <button class="btn-icon-circ hidden md:inline-flex" aria-label="Tìm kiếm">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5"/>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Vào Dashboard
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex btn btn-ghost">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Bắt đầu
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                @endauth

                <button class="btn-icon-circ lg:hidden" aria-label="Menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5"/>
                </button>
            </div>
        </div>
    </header>
</div>
