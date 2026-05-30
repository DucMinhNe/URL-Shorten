<x-guest-layout :title="'Đăng nhập — LinkPay'">
<x-auth-layout eyebrow="Chào mừng quay lại" :title="'Đăng nhập để<br><span class=\'font-light italic text-slate\'>tiếp tục kiếm tiền.</span>'" subtitle="Mỗi giây bạn không đăng nhập là click hợp lệ không được tính. Vào ngay.">

    @if(config('services.google.client_id'))
    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-full border-2 border-hairline hover:border-ink-deep transition-colors">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.07H2.18A10.97 10.97 0 0 0 1 12c0 1.77.42 3.45 1.18 4.93l3.66-2.83z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.83C6.71 7.31 9.14 5.38 12 5.38z"/>
        </svg>
        <span class="type-button-md">Đăng nhập bằng Google</span>
    </a>

    <div class="my-6 flex items-center gap-3">
        <div class="flex-1 border-t border-hairline-soft"></div>
        <span class="type-caption text-stone uppercase tracking-wider">hoặc</span>
        <div class="flex-1 border-t border-hairline-soft"></div>
    </div>
    @endif

    <x-auth-session-status class="mb-4 type-body-sm text-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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

        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="type-body-sm-bold text-ink-deep">Mật khẩu</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="type-body-sm text-primary hover:text-primary-deep">Quên mật khẩu?</a>
                @endif
            </div>
            <div class="relative">
                <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                <input id="password" name="password" type="password" required
                       placeholder="••••••••"
                       class="input pl-11 @error('password') error @enderror"/>
            </div>
            @error('password') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input name="remember" type="checkbox" class="w-4 h-4 rounded border-hairline text-primary focus:ring-primary"/>
            <span class="type-body-sm text-slate">Ghi nhớ tôi trên thiết bị này</span>
        </label>

        <button type="submit" class="btn btn-primary w-full">
            Đăng nhập
            <x-heroicon-m-arrow-right class="w-4 h-4"/>
        </button>
    </form>

    <p class="mt-8 text-center type-body-sm text-slate">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="text-ink-deep font-bold underline underline-offset-2 ml-1">Đăng ký miễn phí →</a>
    </p>
</x-auth-layout>
</x-guest-layout>
