<x-app-layout :title="'Mời bạn bè'">
    <x-slot name="header">Mời bạn bè</x-slot>

    <div class="max-w-[1100px] mx-auto space-y-6">
        <div>
            <div class="section-label mb-3"><span>Chương trình giới thiệu</span></div>
            <h1 class="type-display-lg text-ink-deep">Mời bạn — <span class="lp-grad-text">nhận 10%</span> hoa hồng</h1>
            <p class="type-body-md text-slate mt-3 max-w-[620px]">
                Mỗi người đăng ký qua link của bạn, bạn nhận <b>10%</b> thu nhập của họ trọn đời — không trừ vào tiền của họ.
            </p>
        </div>

        {{-- Referral link card --}}
        <div class="lp-glass-dark !p-7" x-data>
            <div class="blob" style="width:240px;height:240px;background:#696CFF;left:-40px;top:-60px;"></div>
            <div class="blob" style="width:200px;height:200px;background:#EC4899;right:-30px;bottom:-60px;"></div>
            <div class="relative">
                <div class="type-caption-bold uppercase tracking-wider text-white/60">Link giới thiệu của bạn</div>
                <div class="flex flex-col sm:flex-row items-stretch gap-3 mt-3">
                    <div class="flex-1 bg-white/10 border border-white/15 rounded-xl px-4 py-3 font-mono text-sm text-white truncate flex items-center">
                        {{ $link }}
                    </div>
                    <button type="button" class="lp-btn-grad" data-copy="{{ $link }}" data-copy-label="Đã copy link giới thiệu">
                        <x-heroicon-o-clipboard class="w-4 h-4"/> Copy link
                    </button>
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-4">
                    <span class="text-white/60 text-sm">Mã của bạn:</span>
                    <span class="font-mono text-lg font-extrabold tracking-widest text-white">{{ $code }}</span>
                    <span class="lp-tag lp-tag-cyan" data-copy="{{ $code }}" data-copy-label="Đã copy mã">copy mã</span>
                    <div class="ml-auto flex items-center gap-2">
                        <a target="_blank" class="lp-btn-grad cool !py-2 !px-3" style="background:#1877F2"
                           href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($link) }}">
                            <x-heroicon-s-share class="w-4 h-4"/> Facebook
                        </a>
                        <a target="_blank" class="lp-btn-grad !py-2 !px-3"
                           href="https://t.me/share/url?url={{ urlencode($link) }}&text={{ urlencode('Kiếm tiền từ rút gọn link cùng LinkPay!') }}">
                            Telegram
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card-icon-feature lp-kpi lp-accent-cyan !p-5">
                <div class="lp-ic lp-ic-cyan"><x-heroicon-o-users class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Đã mời</div>
                <div class="type-heading-lg text-ink-deep mt-1" data-count="{{ $stats['count'] }}">0</div>
            </div>
            <div class="card-icon-feature lp-kpi lp-accent-green !p-5">
                <div class="lp-ic lp-ic-green"><x-heroicon-o-bolt class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Đang hoạt động</div>
                <div class="type-heading-lg text-ink-deep mt-1" data-count="{{ $stats['active'] }}">0</div>
            </div>
            <div class="card-icon-feature lp-kpi-hero !p-5">
                <div class="lp-ic !bg-white/20"><x-heroicon-s-banknotes class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-white/70 mt-4">Hoa hồng đã nhận</div>
                <div class="type-heading-lg mt-1"><span data-count="{{ $stats['earned'] }}">0</span><span class="type-subtitle-md ml-1">đ</span></div>
            </div>
        </div>

        {{-- Referred users --}}
        <div class="card-feature !p-0 overflow-hidden">
            <div class="p-6 border-b border-hairline-soft">
                <div class="section-label mb-1"><span>Danh sách</span></div>
                <h3 class="type-heading-sm">Bạn bè đã mời</h3>
            </div>
            @if($referrals->isEmpty())
                <div class="p-12 text-center">
                    <x-heroicon-o-user-plus class="w-12 h-12 text-stone mx-auto"/>
                    <p class="type-body-md text-slate mt-3">Chưa có ai đăng ký qua link của bạn. Chia sẻ ngay để bắt đầu nhận hoa hồng!</p>
                </div>
            @else
                <div class="divide-y divide-hairline-soft">
                    @foreach($referrals as $r)
                        <div class="px-6 py-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-deep flex items-center justify-center text-on-dark type-body-sm-bold flex-shrink-0">
                                {{ strtoupper(substr($r->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="type-body-sm-bold text-ink-deep truncate">{{ $r->name }}</div>
                                <div class="type-caption text-stone">Tham gia {{ $r->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-right">
                                <div class="type-body-sm-bold text-success">+{{ number_format((int) ($r->total_earned * 0.1)) }}đ</div>
                                <div class="type-caption text-stone">hoa hồng (10%)</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
