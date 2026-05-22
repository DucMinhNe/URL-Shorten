<footer class="bg-canvas border-t border-hairline-soft border-t-[1px] mt-20">
    <div class="max-w-[1280px] mx-auto px-6 py-16">
        <div class="grid grid-cols-2 md:grid-cols-12 gap-8 md:gap-12">
            {{-- Brand col --}}
            <div class="col-span-2 md:col-span-4">
                <x-brand size="lg"/>
                <p class="type-body-sm text-slate mt-4 max-w-[280px]">
                    Hệ thống rút gọn liên kết kèm quảng cáo. Mỗi 1.000 view hợp lệ — tiền vào ví của bạn.
                </p>

                <form class="mt-6 flex gap-2 max-w-[320px]" onsubmit="event.preventDefault()">
                    <input type="email" placeholder="email@example.com" class="input flex-1"/>
                    <button class="btn btn-primary px-5">
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </button>
                </form>

                <div class="flex items-center gap-2 mt-6">
                    <a href="#" class="btn-icon-circ" aria-label="Facebook">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.198 21.5h4v-8.01h3.604l.396-3.98h-4V7.5a1 1 0 0 1 1-1h3v-4h-3a5 5 0 0 0-5 5v2.01h-2l-.396 3.98h2.396v8.01Z"/></svg>
                    </a>
                    <a href="#" class="btn-icon-circ" aria-label="X">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z"/></svg>
                    </a>
                    <a href="#" class="btn-icon-circ" aria-label="GitHub">
                        <x-heroicon-o-code-bracket class="w-4 h-4"/>
                    </a>
                </div>
            </div>

            {{-- Link columns --}}
            <div class="col-span-1 md:col-span-2">
                <div class="type-body-sm-bold text-ink-deep mb-4">Sản phẩm</div>
                <ul class="space-y-3 type-body-sm text-steel">
                    <li><a href="{{ route('home') }}" class="hover:text-ink-deep">Trang chủ</a></li>
                    <li><a href="#how" class="hover:text-ink-deep">Cách hoạt động</a></li>
                    <li><a href="#pricing" class="hover:text-ink-deep">Bảng giá</a></li>
                    <li><a href="#faq" class="hover:text-ink-deep">FAQ</a></li>
                </ul>
            </div>
            <div class="col-span-1 md:col-span-2">
                <div class="type-body-sm-bold text-ink-deep mb-4">Tài khoản</div>
                <ul class="space-y-3 type-body-sm text-steel">
                    <li><a href="{{ route('login') }}" class="hover:text-ink-deep">Đăng nhập</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-ink-deep">Đăng ký</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-ink-deep">Dashboard</a></li>
                        <li><a href="{{ route('payout.index') }}" class="hover:text-ink-deep">Rút tiền</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-span-1 md:col-span-2">
                <div class="type-body-sm-bold text-ink-deep mb-4">Công ty</div>
                <ul class="space-y-3 type-body-sm text-steel">
                    <li><a href="#" class="hover:text-ink-deep">Giới thiệu</a></li>
                    <li><a href="#" class="hover:text-ink-deep">Liên hệ</a></li>
                    <li><a href="#" class="hover:text-ink-deep">Blog</a></li>
                </ul>
            </div>
            <div class="col-span-1 md:col-span-2">
                <div class="type-body-sm-bold text-ink-deep mb-4">Pháp lý</div>
                <ul class="space-y-3 type-body-sm text-steel">
                    <li><a href="#" class="hover:text-ink-deep">Điều khoản</a></li>
                    <li><a href="#" class="hover:text-ink-deep">Bảo mật</a></li>
                    <li><a href="#" class="hover:text-ink-deep">Cookies</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-hairline-soft flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 type-caption text-stone">
                <button class="pill-tab !py-1 !px-3 !type-caption-bold">🇻🇳 Tiếng Việt</button>
                <span>© 2026 LinkPay · Đồ án sinh viên</span>
            </div>
            <div class="flex items-center gap-4 type-caption text-stone">
                <a href="#" class="hover:text-ink-deep">Điều khoản</a>
                <a href="#" class="hover:text-ink-deep">Bảo mật</a>
                <a href="#" class="hover:text-ink-deep">Cookies</a>
            </div>
        </div>
    </div>
</footer>
