<x-guest-layout :title="'Liên kết bảo mật · LinkPay'">
<div class="min-h-screen flex flex-col bg-surface-soft">
    <header class="bg-canvas border-b border-hairline-soft">
        <div class="max-w-[1400px] mx-auto px-6 h-14 flex items-center justify-between">
            <x-brand size="md"/>
            <a href="{{ route('home') }}" class="type-body-sm text-slate hover:text-ink-deep">Về trang chủ →</a>
        </div>
    </header>

    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[440px]">
            <div class="card-feature text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-primary-soft flex items-center justify-center text-primary-deep mb-6">
                    <x-heroicon-o-lock-closed class="w-9 h-9"/>
                </div>

                <div class="section-label justify-center mb-3"><span>Liên kết bảo mật</span></div>
                <h1 class="type-heading-lg text-ink-deep">Cần mật khẩu<br><span class="font-light italic text-slate">để xem link.</span></h1>
                <p class="type-body-md text-slate mt-3">Người tạo đã đặt mật khẩu bảo vệ. Nhập đúng để tiếp tục đến link gốc.</p>

                <form method="POST" action="{{ route('link.unlock', $slug) }}" class="mt-8 space-y-4">
                    @csrf
                    <div class="relative">
                        <x-heroicon-o-key class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                        <input name="password" type="password" required autofocus placeholder="Nhập mật khẩu"
                               class="input pl-11 text-center type-heading-sm font-bold @error('password') error @enderror"/>
                    </div>
                    @error('password') <p class="type-body-sm text-critical">{{ $message }}</p> @enderror

                    <button type="submit" class="btn btn-primary w-full">
                        <x-heroicon-m-lock-open class="w-4 h-4"/>
                        Mở khoá
                    </button>
                </form>
            </div>

            <p class="mt-5 text-center type-caption text-stone">
                Không biết mật khẩu? Liên hệ người chia sẻ link này.
            </p>
        </div>
    </div>
</div>
</x-guest-layout>
