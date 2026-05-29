<x-guest-layout :title="'LinkPay — Mỗi click là tiền'">
<x-public-nav active="home"/>

{{-- ─────────────────────  HERO  (by.com.vn style) ───────────────────── --}}
<section class="relative overflow-hidden" style="background: linear-gradient(180deg, #F0F4FB 0%, #FAFBFE 100%);">
    {{-- Soft decorative blobs --}}
    <div class="absolute top-32 -left-32 w-96 h-96 rounded-full opacity-30 blur-3xl" style="background: radial-gradient(circle, #FECACA 0%, transparent 70%);"></div>
    <div class="absolute -bottom-40 -right-32 w-[500px] h-[500px] rounded-full opacity-30 blur-3xl" style="background: radial-gradient(circle, #BFDBFE 0%, transparent 70%);"></div>

    <div class="relative max-w-[1280px] mx-auto px-6 pt-16 pb-24 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">

        {{-- LEFT --}}
        <div class="lg:col-span-7 max-w-[640px]">
            {{-- Pink chip pill --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full mb-6" style="background: #FFE0EA; color: #E11D48;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: #E11D48;"></span>
                <span class="text-xs font-bold tracking-wide">Rút gọn link miễn phí</span>
            </div>

            <h1 class="type-hero-display !leading-[1.05]" style="color: #1E293B;">
                Mỗi click là tiền.<br>
                <span style="color: #696CFF;">Liên kết của bạn,</span>
                <span class="italic font-light" style="color: #475569;">lương của bạn.</span>
            </h1>

            <p class="type-subtitle-md mt-5 max-w-[520px]" style="color: #475569;">
                Tạo link ngắn nhanh chóng. Mỗi <strong style="color: #1E293B;">1.000 view hợp lệ</strong> nhận tiền vào ví — chuyển khoản Momo · ZaloPay · PayPal trong 24h.
            </p>

            {{-- Big inline input with pink CTA (by.com.vn style) --}}
            @if(session('shortUrl'))
                <div class="mt-8 p-4 rounded-2xl border-2 flex items-center gap-3" style="background: #ECFDF5; border-color: #10B981;">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #10B981;">
                        <x-heroicon-s-check class="w-5 h-5 text-white"/>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold uppercase tracking-wider" style="color: #047857;">Đã rút gọn thành công</div>
                        <code class="text-base font-mono font-bold truncate block" style="color: #1E293B;">{{ session('shortUrl') }}</code>
                    </div>
                    <button onclick="navigator.clipboard.writeText('{{ session('shortUrl') }}'); this.innerHTML='✓ Đã copy'" class="px-4 py-2 rounded-full font-semibold text-sm flex-shrink-0" style="background: #10B981; color: white;">
                        Copy
                    </button>
                </div>
            @endif

            <form id="shorten" method="POST" action="{{ route('shorten.guest') }}" class="mt-8">
                @csrf
                <div class="flex items-stretch bg-white rounded-full p-1.5 shadow-lg border border-[#E4E7EB]" style="box-shadow: 0 10px 30px -8px rgba(105, 108, 255, 0.15);">
                    <div class="flex items-center pl-5 pr-3 flex-shrink-0">
                        <x-heroicon-o-link class="w-5 h-5" style="color: #94A3B8;"/>
                    </div>
                    <input name="original_url" value="{{ old('original_url') }}" type="url" required
                           placeholder="Dán liên kết dài của bạn..."
                           class="flex-1 py-3 outline-none bg-transparent text-base min-w-0" style="color: #1E293B;"/>
                    <button type="submit" class="px-7 py-3 rounded-full font-bold text-sm whitespace-nowrap flex items-center gap-2 transition-all hover:shadow-lg hover:-translate-y-0.5"
                            style="background: linear-gradient(135deg, #FF4D6D 0%, #E11D48 100%); color: white; box-shadow: 0 4px 12px -2px rgba(225, 29, 72, 0.45);">
                        Rút gọn link
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </button>
                </div>
                @error('original_url') <p class="mt-3 text-sm text-red-600 ml-5">{{ $message }}</p> @enderror

                <div class="mt-3 flex items-center gap-1.5 text-xs ml-5" style="color: #64748B;">
                    <x-heroicon-o-information-circle class="w-3.5 h-3.5"/>
                    Nhấn <strong style="color: #1E293B;">Rút gọn link</strong> nghĩa là bạn đã đồng ý với
                    <a href="#" class="underline underline-offset-2 font-semibold" style="color: #696CFF;">điều khoản sử dụng</a>.
                </div>
            </form>

            {{-- Trust bar --}}
            <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm" style="color: #64748B;">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center" style="background: #DCFCE7;">
                        <x-heroicon-s-check class="w-4 h-4" style="color: #16A34A;"/>
                    </div>
                    Không cần đăng ký
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center" style="background: #DBEAFE;">
                        <x-heroicon-s-bolt class="w-4 h-4" style="color: #2563EB;"/>
                    </div>
                    Tốc độ &lt;200ms
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center" style="background: #FCE7F3;">
                        <x-heroicon-s-shield-check class="w-4 h-4" style="color: #DB2777;"/>
                    </div>
                    Chống spam · bot
                </div>
            </div>
        </div>

        {{-- RIGHT: Cartoon illustration --}}
        <div class="lg:col-span-5">
            <x-landing-illustration class=""/>
        </div>
    </div>
</section>

{{-- Ticker (real-time payouts) --}}
<section class="bg-white border-y border-[#E4E7EB] overflow-hidden">
    <div class="flex animate-marquee whitespace-nowrap py-3.5">
        @php
            $payouts_top = [
                ['nguyen****@gmail', '250.000đ', 'Momo'],
                ['hoa***@yahoo', '100.000đ', 'ZaloPay'],
                ['minh***@hotmail', '$12 USD', 'PayPal'],
            ];
        @endphp
        @for ($i = 0; $i < 2; $i++)
            @foreach($payouts_top as $p)
                <span class="inline-flex items-center gap-2 mx-6 text-sm">
                    <span class="w-2 h-2 rounded-full" style="background: #10B981;"></span>
                    <span style="color: #64748B;">{{ $p[0] }}</span>
                    <span class="font-bold" style="color: #1E293B;">nhận {{ $p[1] }}</span>
                    <span style="color: #94A3B8;">qua {{ $p[2] }}</span>
                    <span style="color: #CBD5E1;">·</span>
                </span>
            @endforeach
        @endfor
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
                <div class="aspect-[4/3] flex items-center justify-center relative" style="background: radial-gradient(circle at 30% 30%, #ff4dab 0%, #ea27c2 50%, #a50064 100%);">
                    <img src="{{ asset('images/payment/momo.svg') }}" alt="MoMo" class="w-28 h-28 drop-shadow-xl">
                    <span class="absolute top-4 right-4 badge badge-success">
                        <x-heroicon-s-check class="w-3 h-3"/> Phổ biến
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="type-heading-sm">MoMo</h3>
                    <p class="type-body-md text-slate mt-2">Chuyển khoản qua số điện thoại MoMo. Tối thiểu 100.000đ.</p>
                    <div class="mt-4 flex items-center gap-2 type-caption-bold text-slate">
                        <x-heroicon-s-clock class="w-4 h-4"/>
                        Duyệt trong 24h · Phí 0đ
                    </div>
                </div>
            </div>

            {{-- ZaloPay --}}
            <div class="card-photo border border-hairline-soft">
                <div class="aspect-[4/3] flex items-center justify-center px-10" style="background: linear-gradient(135deg, #00ABFC 0%, #0068FF 100%);">
                    <img src="{{ asset('images/payment/zalopay.svg') }}" alt="ZaloPay" class="max-w-[80%] max-h-[60%] drop-shadow-xl">
                </div>
                <div class="p-8">
                    <h3 class="type-heading-sm">ZaloPay</h3>
                    <p class="type-body-md text-slate mt-2">Chuyển khoản qua tài khoản ZaloPay. Tối thiểu 100.000đ.</p>
                    <div class="mt-4 flex items-center gap-2 type-caption-bold text-slate">
                        <x-heroicon-s-clock class="w-4 h-4"/>
                        Duyệt trong 24h · Phí 0đ
                    </div>
                </div>
            </div>

            {{-- PayPal --}}
            <div class="card-photo border border-hairline-soft">
                <div class="aspect-[4/3] flex items-center justify-center px-10" style="background: linear-gradient(135deg, #003087 0%, #001F5C 100%);">
                    <img src="{{ asset('images/payment/paypal.svg') }}" alt="PayPal" class="max-w-[75%] max-h-[55%] drop-shadow-xl brightness-0 invert">
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
