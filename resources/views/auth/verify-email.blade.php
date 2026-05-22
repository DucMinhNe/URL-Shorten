<x-guest-layout :title="'Xác thực email — LinkPay'">
<div class="min-h-screen flex flex-col">
    <header class="px-8 py-6 flex items-center justify-between border-b border-hairline-soft">
        <x-brand size="md"/>
    </header>

    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[480px]">
            <div class="card-feature text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-primary-soft flex items-center justify-center text-primary-deep mb-6">
                    <x-heroicon-o-envelope-open class="w-12 h-12"/>
                </div>

                <div class="section-label justify-center mb-3"><span>Bước cuối</span></div>
                <h1 class="type-heading-lg text-ink-deep">Kiểm tra hộp thư<br><span class="font-light italic text-slate">của bạn.</span></h1>
                <p class="type-body-md text-slate mt-4">
                    Bọn tao đã gửi link xác thực tới <strong class="text-ink-deep">{{ auth()->user()?->email ?? 'email của bạn' }}</strong>. Click vào đó để mở khoá tính năng kiếm tiền.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mt-6 p-4 rounded-xl bg-[color:var(--color-success-soft)] border border-success/30 text-success type-body-sm-bold flex items-center gap-2 justify-center">
                        <x-heroicon-s-check-circle class="w-5 h-5"/>
                        Email xác thực mới đã được gửi
                    </div>
                @endif

                <div class="mt-8 space-y-3">
                    <a href="https://mail.google.com" target="_blank" class="btn btn-primary w-full">
                        <x-heroicon-o-envelope class="w-4 h-4"/>
                        Mở Gmail
                    </a>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost w-full">
                            <x-heroicon-o-arrow-path class="w-4 h-4"/>
                            Gửi lại email xác thực
                        </button>
                    </form>
                </div>

                <div class="mt-6 pt-6 border-t border-hairline-soft flex items-center justify-center gap-4 type-body-sm">
                    <a href="{{ route('profile.edit') }}" class="text-slate hover:text-ink-deep">Đổi email</a>
                    <span class="text-stone">·</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate hover:text-ink-deep">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
