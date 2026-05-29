<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Dashboard' }} · LinkPay</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('head')

    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-surface-soft min-h-screen">
    <div x-data="{ menuOpen: false }" class="flex min-h-screen">

        {{-- MOBILE OVERLAY --}}
        <div x-show="menuOpen" x-cloak x-transition.opacity
             x-on:click="menuOpen = false"
             class="fixed inset-0 z-30 bg-ink-deep/60 backdrop-blur-sm lg:hidden"
             aria-hidden="true"></div>

        {{-- SIDEBAR --}}
        <aside
            x-cloak
            class="fixed lg:static inset-y-0 left-0 z-40 flex flex-col w-60 bg-canvas border-r border-hairline-soft transform transition-transform duration-200 ease-out"
            :class="menuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="h-16 px-6 flex items-center justify-between border-b border-hairline-soft">
                <x-brand size="md"/>
                <button type="button" x-on:click="menuOpen = false" class="btn-icon-ghost lg:hidden" aria-label="Đóng menu">
                    <x-heroicon-m-x-mark class="w-4 h-4"/>
                </button>
            </div>

            @php
                $route = request()->route()?->getName() ?? '';
                $isActive = function($prefix) use ($route) {
                    if (is_array($prefix)) {
                        foreach ($prefix as $p) if (str_starts_with($route, $p)) return true;
                        return false;
                    }
                    return str_starts_with($route, $prefix);
                };
            @endphp

            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <div class="px-3 mb-2 type-caption-bold uppercase tracking-wider text-stone">Tổng quan</div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl type-body-sm-bold {{ $isActive('dashboard') ? 'bg-primary-soft text-primary-deep' : 'text-charcoal hover:bg-surface-soft' }}">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5"/>
                    Dashboard
                </a>
                <a href="{{ route('links.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl type-body-sm-bold {{ $isActive('links') ? 'bg-primary-soft text-primary-deep' : 'text-charcoal hover:bg-surface-soft' }}">
                    <x-heroicon-o-link class="w-5 h-5"/>
                    Liên kết của tôi
                    @auth
                        <span class="ml-auto badge badge-neutral !py-0.5 !px-2">{{ auth()->user()->shortLinks()->count() }}</span>
                    @endauth
                </a>
                <a href="{{ route('payout.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl type-body-sm-bold {{ $isActive('payout') ? 'bg-primary-soft text-primary-deep' : 'text-charcoal hover:bg-surface-soft' }}">
                    <x-heroicon-o-banknotes class="w-5 h-5"/>
                    Rút tiền
                </a>

                <div class="px-3 mt-6 mb-2 type-caption-bold uppercase tracking-wider text-stone">Tài khoản</div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl type-body-sm-bold {{ $isActive('profile') ? 'bg-primary-soft text-primary-deep' : 'text-charcoal hover:bg-surface-soft' }}">
                    <x-heroicon-o-user-circle class="w-5 h-5"/>
                    Hồ sơ
                </a>
                @if(auth()->user()?->is_admin)
                    <a href="/admin" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl type-body-sm-bold text-charcoal hover:bg-surface-soft">
                        <x-heroicon-o-shield-check class="w-5 h-5"/>
                        Admin Panel
                        <x-heroicon-m-arrow-up-right class="w-4 h-4 ml-auto text-stone"/>
                    </a>
                @endif
            </nav>

            {{-- Bottom user card --}}
            <div class="p-3 border-t border-hairline-soft">
                <div class="card-icon-feature !p-3 !rounded-2xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-primary-deep flex items-center justify-center text-on-dark type-body-sm-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="type-body-sm-bold text-ink-deep truncate">{{ auth()->user()->name }}</div>
                        <div class="type-caption text-stone truncate">{{ number_format(auth()->user()->balance ?? 0) }}đ</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-icon-ghost" title="Đăng xuất">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4"/>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN --}}
        <div class="flex-1 min-w-0 flex flex-col">
            <header class="h-16 bg-canvas border-b border-hairline-soft px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" x-on:click="menuOpen = true" class="btn-icon-circ lg:hidden" aria-label="Mở menu">
                        <x-heroicon-o-bars-3 class="w-5 h-5"/>
                    </button>
                    @isset($header)
                        <div class="type-heading-sm text-ink-deep truncate">{{ $header }}</div>
                    @endisset
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    {{-- Search (real form posts to /links?q=) --}}
                    <form method="GET" action="{{ route('links.index') }}" class="hidden md:flex items-center gap-2 search-pill !w-64">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-steel ml-2"/>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm liên kết..."
                               class="bg-transparent border-0 outline-none flex-1 type-body-sm w-full"/>
                    </form>

                    {{-- Balance pill --}}
                    <a href="{{ route('payout.index') }}" class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-full bg-primary-soft text-primary-deep type-body-sm-bold">
                        <x-heroicon-s-banknotes class="w-4 h-4"/>
                        {{ number_format(auth()->user()->balance ?? 0) }}đ
                    </a>

                    {{-- Quick action --}}
                    <a href="{{ route('links.create') }}" class="btn btn-primary !py-2 !px-4">
                        <x-heroicon-m-plus class="w-4 h-4"/>
                        <span class="hidden sm:inline">Tạo liên kết</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
