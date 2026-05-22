<x-guest-layout :title="'LinkPay — Mỗi click là tiền'">
<x-public-nav active="home"/>

{{-- ─────────────────────  HERO  ───────────────────── --}}
<section class="bg-canvas">
    <div class="max-w-[1280px] mx-auto px-6 pt-16 pb-20 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

        {{-- LEFT: Editorial copy --}}
        <div class="lg:col-span-7">
            <div class="section-label mb-6">
                <span>Earn from every click · Việt Nam</span>
            </div>

            <h1 class="type-hero-display text-ink-deep">
                Mỗi click là tiền.
                <span class="block font-light italic text-slate">Liên kết của bạn, lương của bạn.</span>
            </h1>

            <p class="type-subtitle-md text-charcoal mt-6 max-w-[560px]">
                Rút gọn link, chèn quảng cáo tự động. Hệ thống trả bạn theo mỗi 1.000 lượt xem hợp lệ — chuyển khoản qua Momo, ZaloPay hoặc PayPal trong 24h.
            </p>

            <div class="mt-10 flex flex-wrap items-center gap-3">
                <a href="#shorten" class="btn btn-primary">
                    Bắt đầu rút gọn miễn phí
                    <x-heroicon-m-arrow-right class="w-4 h-4"/>
                </a>
                <a href="#how" class="btn btn-secondary">
                    <x-heroicon-m-play-circle class="w-4 h-4"/>
                    Xem cách hoạt động
                </a>
            </div>

            <ul class="mt-10 flex flex-wrap gap-x-8 gap-y-3 type-body-sm text-slate">
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-success"/>
                    Không cần đăng ký
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-success"/>
                    Rút từ 100.000đ
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-4 h-4 text-success"/>
                    Chống fraud Cloudflare
                </li>
            </ul>
        </div>

        {{-- RIGHT: Shorten form card --}}
        <div class="lg:col-span-5">
            <div id="shorten" class="card-feature relative">
                {{-- Floating badge --}}
                <div class="absolute -top-3 left-8">
                    <span class="badge badge-warning">
                        <x-heroicon-s-bolt class="w-3 h-3"/>
                        Khởi tạo trong 5 giây
                    </span>
                </div>

                <h3 class="type-heading-sm">Rút gọn liên kết</h3>
                <p class="type-body-sm text-slate mt-1">Dán URL gốc, lấy link ngắn — tiền vào ví khi có người click.</p>

                @if(session('shortUrl'))
                    <div class="mt-6 p-4 rounded-xl bg-[color:var(--color-success-soft)] border border-[color:var(--color-success)]/30">
                        <div class="type-caption-bold text-success uppercase tracking-wider mb-1">Đã rút gọn ✓</div>
                        <div class="flex items-center gap-2">
                            <code class="font-mono text-ink-deep type-body-md-bold truncate flex-1">{{ session('shortUrl') }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ session('shortUrl') }}'); this.innerHTML='Đã copy ✓'" class="btn btn-ghost !py-2 !px-3 !text-xs">
                                <x-heroicon-o-clipboard class="w-4 h-4"/>
                                Copy
                            </button>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('shorten.guest') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label class="type-body-sm-bold text-ink-deep block mb-2">URL gốc</label>
                        <div class="relative">
                            <x-heroicon-o-link class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                            <input name="original_url" value="{{ old('original_url') }}" type="url" required
                                   placeholder="https://example.com/bai-viet-cua-toi"
                                   class="input pl-11 @error('original_url') error @enderror"/>
                        </div>
                        @error('original_url') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="type-body-sm-bold text-ink-deep block mb-2 flex items-center justify-between">
                            <span>Alias tuỳ chỉnh</span>
                            <span class="type-caption text-stone font-normal">tuỳ chọn</span>
                        </label>
                        <div class="flex items-stretch rounded-lg border border-hairline overflow-hidden focus-within:border-[color:var(--color-fb-blue)] focus-within:border-2">
                            <span class="bg-surface-soft px-4 flex items-center type-body-sm text-slate font-mono">linkpay.vn/</span>
                            <input name="custom_alias" value="{{ old('custom_alias') }}" type="text" pattern="[A-Za-z0-9_-]{3,32}"
                                   placeholder="ten-cua-ban"
                                   class="flex-1 px-3 type-body-md outline-none bg-canvas font-mono"/>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        Rút gọn ngay
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </button>

                    @guest
                        <p class="type-caption text-stone text-center pt-1">
                            <a href="{{ route('register') }}" class="text-ink-deep font-bold underline underline-offset-2">Đăng ký miễn phí</a>
                            để kiếm tiền từ liên kết của bạn
                        </p>
                    @endguest
                </form>
            </div>

            {{-- Live counter chip --}}
            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-surface-soft rounded-full type-body-sm">
                <span class="w-2 h-2 rounded-full bg-success pulse-dot"></span>
                <span class="text-slate"><span class="font-bold text-ink-deep" id="live-count">4.523</span> lượt rút gọn hôm nay</span>
            </div>
        </div>
    </div>

    {{-- Ticker --}}
    <div class="bg-surface-soft border-y border-hairline-soft overflow-hidden">
        <div class="flex animate-marquee whitespace-nowrap py-3.5">
            @php
                $payouts = [
                    ['👤', 'nguyen****@gmail', '250.000đ', 'Momo'],
                    ['👤', 'hoa***@yahoo', '100.000đ', 'ZaloPay'],
                    ['👤', 'minh***@hotmail', '$12 USD', 'PayPal'],
                    ['👤', 'phuc***@gmail', '500.000đ', 'Momo'],
                    ['👤', 'linh***@outlook', '150.000đ', 'ZaloPay'],
                    ['👤', 'tuan***@gmail', '300.000đ', 'Momo'],
                    ['👤', 'hieu***@yahoo', '$8 USD', 'PayPal'],
                    ['👤', 'mai***@gmail', '200.000đ', 'ZaloPay'],
                ];
            @endphp
            @for ($i = 0; $i < 2; $i++)
                @foreach($payouts as $p)
                    <span class="inline-flex items-center gap-2 mx-6 type-body-sm font-mono">
                        <x-heroicon-s-banknotes class="w-4 h-4 text-success"/>
                        <span class="text-slate">{{ $p[1] }}</span>
                        <span class="text-ink-deep font-bold">nhận {{ $p[2] }}</span>
                        <span class="text-stone">qua {{ $p[3] }}</span>
                        <span class="text-stone">·</span>
                    </span>
                @endforeach
            @endfor
        </div>
    </div>
</section>

{{-- ─────────────────────  HOW IT WORKS  ───────────────────── --}}
<section id="how" class="bg-canvas py-20 lg:py-28">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="max-w-[720px]">
            <div class="section-label mb-4"><span>01 — Cách hoạt động</span></div>
            <h2 class="type-display-lg text-ink-deep">Ba bước. Một liên kết. <span class="font-light italic text-slate">Tiền vào ví.</span></h2>
            <p class="type-subtitle-md text-charcoal mt-6 max-w-[560px]">
                Từ lúc dán link gốc đến lúc nhận tiền vào Momo — toàn bộ flow tự động chạy nền.
            </p>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Step 1 --}}
            <div class="card-feature !p-8 relative">
                <div class="font-mono type-heading-lg text-primary">01.</div>
                <div class="mt-6 inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-soft text-primary-deep">
                    <x-heroicon-o-link class="w-7 h-7"/>
                </div>
                <h3 class="type-heading-sm mt-6">Dán link gốc</h3>
                <p class="type-body-md text-slate mt-2">
                    Bất kỳ URL nào — bài viết, video, file PDF. Đặt alias dễ nhớ như <span class="font-mono text-ink-deep">/cv-toi</span> nếu muốn.
                </p>

                <div class="mt-6 p-3 rounded-lg bg-surface-soft font-mono type-body-sm text-slate truncate">
                    https://shopee.vn/sale-9-9-...
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="card-feature !p-8 relative">
                <div class="font-mono type-heading-lg text-primary">02.</div>
                <div class="mt-6 inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-soft text-primary-deep">
                    <x-heroicon-o-share class="w-7 h-7"/>
                </div>
                <h3 class="type-heading-sm mt-6">Chia sẻ liên kết ngắn</h3>
                <p class="type-body-md text-slate mt-2">
                    Đăng Facebook, Zalo, TikTok bio, Telegram. Người click sẽ xem quảng cáo 5 giây trước khi đến link gốc.
                </p>

                <div class="mt-6 flex items-center gap-2 p-3 rounded-lg bg-ink-deep text-on-dark font-mono type-body-sm">
                    <x-heroicon-s-link class="w-4 h-4"/>
                    linkpay.vn/khuyenmai
                    <span class="ml-auto type-caption-bold text-[color:var(--color-warning)]">+ COPY</span>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="card-feature !p-8 relative">
                <div class="font-mono type-heading-lg text-primary">03.</div>
                <div class="mt-6 inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-soft text-primary-deep">
                    <x-heroicon-o-banknotes class="w-7 h-7"/>
                </div>
                <h3 class="type-heading-sm mt-6">Nhận tiền vào ví</h3>
                <p class="type-body-md text-slate mt-2">
                    5.000đ cho mỗi 1.000 view hợp lệ. Rút từ 100.000đ qua Momo, ZaloPay hoặc PayPal — admin duyệt trong 24h.
                </p>

                <div class="mt-6 p-3 rounded-lg bg-[color:var(--color-success-soft)] font-mono type-body-sm-bold text-success flex items-center gap-2">
                    <x-heroicon-s-arrow-trending-up class="w-4 h-4"/>
                    + 247.500đ tháng này
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────  STATS BAND  ───────────────────── --}}
<section class="py-20">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="card-promo-dark !p-10 md:!p-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-on-dark">
                <div>
                    <div class="type-caption-bold text-stone uppercase tracking-wider mb-3">Lượt click đã trả</div>
                    <div class="type-display-lg text-on-dark">17.6K+</div>
                </div>
                <div>
                    <div class="type-caption-bold text-stone uppercase tracking-wider mb-3">VND / 1.000 view</div>
                    <div class="type-display-lg text-on-dark">5.000đ</div>
                </div>
                <div>
                    <div class="type-caption-bold text-stone uppercase tracking-wider mb-3">Duyệt rút tiền</div>
                    <div class="type-display-lg text-on-dark">&lt; 24h</div>
                </div>
                <div>
                    <div class="type-caption-bold text-stone uppercase tracking-wider mb-3">Phương thức</div>
                    <div class="type-display-lg text-on-dark">3</div>
                    <div class="type-body-sm text-stone mt-1">Momo · Zalo · PayPal</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────  PAYOUT METHODS  ───────────────────── --}}
<section id="pricing" class="py-20 lg:py-28">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="max-w-[720px] mb-16">
            <div class="section-label mb-4"><span>02 — Rút tiền</span></div>
            <h2 class="type-display-lg text-ink-deep">Ba kênh thanh toán. <span class="font-light italic text-slate">Không phí ẩn.</span></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Momo --}}
            <div class="card-photo border border-hairline-soft">
                <div class="aspect-[4/3] bg-gradient-to-br from-pink-500 to-pink-700 flex items-center justify-center relative">
                    <div class="w-24 h-24 bg-canvas rounded-3xl flex items-center justify-center shadow-lg">
                        <span class="font-black text-3xl text-pink-600">M</span>
                    </div>
                    <span class="absolute top-4 right-4 badge badge-success">
                        <x-heroicon-s-check class="w-3 h-3"/> Phổ biến
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="type-heading-sm">Momo</h3>
                    <p class="type-body-md text-slate mt-2">Chuyển khoản qua số điện thoại Momo. Tối thiểu 100.000đ.</p>
                    <div class="mt-4 flex items-center gap-2 type-caption-bold text-slate">
                        <x-heroicon-s-clock class="w-4 h-4"/>
                        Duyệt trong 24h · Phí 0đ
                    </div>
                </div>
            </div>

            {{-- ZaloPay --}}
            <div class="card-photo border border-hairline-soft">
                <div class="aspect-[4/3] bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                    <div class="w-24 h-24 bg-canvas rounded-3xl flex items-center justify-center shadow-lg">
                        <span class="font-black text-3xl text-blue-600">Z</span>
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="type-heading-sm">ZaloPay</h3>
                    <p class="type-body-md text-slate mt-2">Chuyển khoản qua số ZaloPay. Tối thiểu 100.000đ.</p>
                    <div class="mt-4 flex items-center gap-2 type-caption-bold text-slate">
                        <x-heroicon-s-clock class="w-4 h-4"/>
                        Duyệt trong 24h · Phí 0đ
                    </div>
                </div>
            </div>

            {{-- PayPal --}}
            <div class="card-photo border border-hairline-soft">
                <div class="aspect-[4/3] bg-gradient-to-br from-indigo-700 to-indigo-900 flex items-center justify-center">
                    <div class="w-24 h-24 bg-canvas rounded-3xl flex items-center justify-center shadow-lg">
                        <span class="font-black text-3xl text-indigo-700">P</span>
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="type-heading-sm">PayPal</h3>
                    <p class="type-body-md text-slate mt-2">Chuyển USD qua email PayPal. Tối thiểu $4 USD (~100k VND).</p>
                    <div class="mt-4 flex items-center gap-2 type-caption-bold text-slate">
                        <x-heroicon-s-globe-alt class="w-4 h-4"/>
                        Quốc tế · Tỷ giá 25k/$1
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────  FEATURES GRID  ───────────────────── --}}
<section class="py-20 lg:py-28 bg-surface-soft">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="max-w-[720px] mb-16">
            <div class="section-label mb-4"><span>Vì sao chọn LinkPay</span></div>
            <h2 class="type-display-lg text-ink-deep">Bốn lý do mọi creator <span class="font-light italic text-slate">đang dùng.</span></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-icon-feature">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-canvas border border-hairline-soft">
                    <x-heroicon-o-shield-check class="w-6 h-6 text-primary"/>
                </div>
                <h3 class="type-subtitle-lg mt-5">Chống gian lận</h3>
                <p class="type-body-sm text-slate mt-2">Cloudflare Turnstile + IP dedup 24h ngăn bot và tự click — view hợp lệ mới tính tiền.</p>
            </div>
            <div class="card-icon-feature">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-canvas border border-hairline-soft">
                    <x-heroicon-o-lock-closed class="w-6 h-6 text-primary"/>
                </div>
                <h3 class="type-subtitle-lg mt-5">Mật khẩu link</h3>
                <p class="type-body-sm text-slate mt-2">Bảo vệ link nhạy cảm với passcode. Chỉ người có mật khẩu mới mở được.</p>
            </div>
            <div class="card-icon-feature">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-canvas border border-hairline-soft">
                    <x-heroicon-o-sparkles class="w-6 h-6 text-primary"/>
                </div>
                <h3 class="type-subtitle-lg mt-5">Alias tuỳ chỉnh</h3>
                <p class="type-body-sm text-slate mt-2">linkpay.vn/<span class="font-mono">ten-cua-ban</span> thay vì chuỗi random — dễ nhớ, dễ share.</p>
            </div>
            <div class="card-icon-feature">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-canvas border border-hairline-soft">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-primary"/>
                </div>
                <h3 class="type-subtitle-lg mt-5">Thống kê chi tiết</h3>
                <p class="type-body-sm text-slate mt-2">Click, view hợp lệ, doanh thu — biểu đồ 30 ngày + breakdown từng link.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────  FAQ  ───────────────────── --}}
<section id="faq" class="py-20 lg:py-28">
    <div class="max-w-[760px] mx-auto px-6">
        <div class="mb-12">
            <div class="section-label mb-4"><span>03 — Câu hỏi thường gặp</span></div>
            <h2 class="type-display-lg text-ink-deep">Bạn còn <span class="font-light italic text-slate">băn khoăn?</span></h2>
        </div>

        <div class="space-y-3">
            @php
                $faqs = [
                    ['q' => 'Bao lâu mới có thể rút tiền?', 'a' => 'Khi balance đạt 100.000đ (Momo/Zalo) hoặc $4 (PayPal), bạn gửi yêu cầu rút. Admin duyệt và chuyển trong vòng 24h.'],
                    ['q' => 'Ai chi tiền? Quảng cáo từ đâu?', 'a' => 'Đối tác quảng cáo trả phí khi banner của họ được hiển thị trên trang interstitial. LinkPay giữ một phần để vận hành, phần còn lại trả cho người tạo link theo CPM cố định 5.000đ/1.000 view.'],
                    ['q' => 'Tôi có thể tự click link của mình để kiếm tiền không?', 'a' => 'Không. Hệ thống tự nhận diện self-click qua user account và IP. Click không hợp lệ vẫn được ghi nhận nhưng không cộng tiền.'],
                    ['q' => 'Link rút gọn có hết hạn không?', 'a' => 'Mặc định không hết hạn. Bạn có thể tự tắt link bất kỳ lúc nào từ dashboard. Admin có quyền chặn link vi phạm chính sách.'],
                    ['q' => 'Có phí ẩn không?', 'a' => 'Hoàn toàn không. 0đ phí tạo link, 0đ phí rút tiền. Bạn chỉ phải chịu phí ngân hàng nếu PayPal có (~$0.30 cho giao dịch quốc tế).'],
                ];
            @endphp

            @foreach($faqs as $faq)
                <details class="group card-icon-feature !p-0" >
                    <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                        <span class="type-subtitle-lg text-ink-deep pr-4">{{ $faq['q'] }}</span>
                        <span class="btn-icon-ghost flex-shrink-0 group-open:rotate-180 transition-transform">
                            <x-heroicon-o-chevron-down class="w-5 h-5"/>
                        </span>
                    </summary>
                    <div class="px-6 pb-6 type-body-md text-slate -mt-2">
                        {{ $faq['a'] }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────────────────  CTA BAND  ───────────────────── --}}
<section class="py-20">
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="card-promo-dark relative overflow-hidden">
            {{-- Decorative blobs --}}
            <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-primary opacity-20 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-[color:var(--color-warning)] opacity-10 blur-3xl"></div>

            <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7">
                    <h2 class="type-display-lg text-on-dark">
                        Sẵn sàng biến mỗi click <span class="font-light italic text-stone">thành tiền?</span>
                    </h2>
                    <p class="type-subtitle-md text-stone mt-4 max-w-[500px]">
                        Tạo tài khoản 30 giây. Không thẻ tín dụng. Welcome bonus 50.000đ chờ sẵn trong ví.
                    </p>
                </div>
                <div class="lg:col-span-5 flex flex-wrap gap-3 lg:justify-end">
                    <a href="{{ route('register') }}" class="btn btn-buy">
                        Tạo tài khoản miễn phí
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-ghost !border-stone/40 !text-on-dark hover:!bg-white/10">
                        Đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<x-public-footer/>
</x-guest-layout>
