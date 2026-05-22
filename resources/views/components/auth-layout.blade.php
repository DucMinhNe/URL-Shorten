@props(['eyebrow' => null, 'title' => '', 'subtitle' => null])

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- LEFT: Form column --}}
    <div class="flex flex-col bg-canvas">
        <div class="px-8 py-6 flex items-center justify-between border-b border-hairline-soft">
            <x-brand size="md"/>
            <a href="{{ route('home') }}" class="type-body-sm text-slate hover:text-ink-deep flex items-center gap-1">
                <x-heroicon-m-arrow-left class="w-4 h-4"/>
                Về trang chủ
            </a>
        </div>

        <div class="flex-1 flex items-center justify-center px-8 py-12">
            <div class="w-full max-w-[420px]">
                @if($eyebrow)
                    <div class="section-label mb-5"><span>{{ $eyebrow }}</span></div>
                @endif
                <h1 class="type-display-lg text-ink-deep">{!! $title !!}</h1>
                @if($subtitle)
                    <p class="type-subtitle-md text-charcoal mt-4">{{ $subtitle }}</p>
                @endif

                <div class="mt-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Visual column --}}
    <div class="hidden lg:block relative overflow-hidden bg-ink-deep">
        {{-- Background gradient blobs --}}
        <div class="absolute inset-0">
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full bg-primary opacity-30 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-32 w-[500px] h-[500px] rounded-full bg-[color:var(--color-warning)] opacity-15 blur-3xl"></div>
        </div>

        {{-- Decorative grid --}}
        <svg class="absolute inset-0 w-full h-full opacity-[0.06]" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>

        {{-- Content overlay --}}
        <div class="relative h-full flex flex-col justify-between p-12 text-on-dark">
            {{-- Top quote --}}
            <div class="max-w-[420px]">
                <div class="flex items-center gap-2 mb-6">
                    <span class="w-2 h-2 rounded-full bg-[color:var(--color-warning)] pulse-dot"></span>
                    <span class="type-caption-bold uppercase tracking-wider text-stone">Người dùng thật · cập nhật trực tiếp</span>
                </div>
                <div class="type-heading-md text-on-dark font-light">
                    "Tao kiếm <span class="font-medium text-[color:var(--color-warning)]">400k/tháng</span> chỉ từ share link bài tập trong group lớp. Đủ tiền cafe mỗi ngày."
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-400 to-orange-400 flex items-center justify-center type-body-md-bold text-on-dark">KN</div>
                    <div>
                        <div class="type-body-sm-bold text-on-dark">Khoa N.</div>
                        <div class="type-caption text-stone">Sinh viên IT · Đà Nẵng</div>
                    </div>
                </div>
            </div>

            {{-- Bottom stats card --}}
            <div class="card-summary !bg-white/10 !backdrop-blur-md !border-white/20 !shadow-none">
                <div class="type-caption-bold text-stone uppercase tracking-wider mb-3">Tổng tiền đã trả creator</div>
                <div class="flex items-end justify-between">
                    <div class="type-display-lg text-on-dark">1.872.500đ</div>
                    <div class="type-body-sm-bold text-success">+12.4% tuần này ↑</div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <div class="flex -space-x-2">
                        @foreach(['from-pink-400 to-orange-400','from-blue-400 to-purple-500','from-green-400 to-blue-500','from-yellow-400 to-pink-400'] as $g)
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br {{ $g }} border-2 border-ink-deep"></div>
                        @endforeach
                    </div>
                    <span class="type-caption text-stone">50+ creator đang nhận tiền</span>
                </div>
            </div>
        </div>
    </div>
</div>
