<x-guest-layout :title="'Quên mật khẩu — LinkPay'">
<div class="min-h-screen flex flex-col">
    <header class="px-8 py-6 flex items-center justify-between border-b border-hairline-soft">
        <x-brand size="md"/>
        <a href="{{ route('login') }}" class="type-body-sm text-slate hover:text-ink-deep flex items-center gap-1">
            <x-heroicon-m-arrow-left class="w-4 h-4"/>
            Quay lại đăng nhập
        </a>
    </header>

    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[480px]">
            <div class="card-feature">
                <div class="w-16 h-16 rounded-full bg-primary-soft flex items-center justify-center text-primary-deep mb-6">
                    <x-heroicon-o-key class="w-7 h-7"/>
                </div>

                <div class="section-label mb-3"><span>Reset mật khẩu</span></div>
                <h1 class="type-heading-lg text-ink-deep">Quên mật khẩu?<br><span class="font-light italic text-slate">Chuyện thường.</span></h1>
                <p class="type-body-md text-slate mt-4">Nhập email đã đăng ký. Bọn tao gửi link reset vào hộp thư trong 1 phút.</p>

                <x-auth-session-status class="mt-4 type-body-sm text-success" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="type-body-sm-bold text-ink-deep block mb-2">Email</label>
                        <div class="relative">
                            <x-heroicon-o-envelope class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                   placeholder="email@example.com"
                                   class="input pl-11 @error('email') error @enderror"/>
                        </div>
                        @error('email') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        Gửi link reset
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center type-body-sm text-slate">
                Không nhận được mail? Check spam folder hoặc <a href="#" class="text-ink-deep font-bold underline">liên hệ support</a>
            </p>
        </div>
    </div>
</div>
</x-guest-layout>
