<x-guest-layout :title="'Xác nhận mật khẩu — LinkPay'">
<div class="min-h-screen flex flex-col">
    <header class="px-8 py-6 flex items-center justify-between border-b border-hairline-soft">
        <x-brand size="md"/>
        <a href="{{ url()->previous() }}" class="type-body-sm text-slate hover:text-ink-deep flex items-center gap-1">
            <x-heroicon-m-arrow-left class="w-4 h-4"/>
            Quay lại
        </a>
    </header>

    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[480px]">
            <div class="card-feature">
                <div class="w-16 h-16 rounded-full bg-primary-soft flex items-center justify-center text-primary-deep mb-6">
                    <x-heroicon-o-shield-check class="w-7 h-7"/>
                </div>

                <div class="section-label mb-3"><span>Vùng bảo mật</span></div>
                <h1 class="type-heading-lg text-ink-deep">Xác nhận mật khẩu<br><span class="font-light italic text-slate">trước khi tiếp tục.</span></h1>
                <p class="type-body-md text-slate mt-4">Đây là khu vực nhạy cảm. Vui lòng nhập lại mật khẩu để xác nhận đây là bạn.</p>

                <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="password" class="type-body-sm-bold text-ink-deep block mb-2">Mật khẩu</label>
                        <div class="relative">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                            <input id="password" name="password" type="password" required autofocus autocomplete="current-password"
                                   placeholder="Nhập mật khẩu"
                                   class="input pl-11 @error('password') error @enderror"/>
                        </div>
                        @error('password') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        Xác nhận
                        <x-heroicon-m-check class="w-4 h-4"/>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
