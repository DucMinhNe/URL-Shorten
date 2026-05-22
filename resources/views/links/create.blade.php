<x-app-layout :title="'Tạo liên kết mới'">
    <x-slot name="header">Tạo liên kết mới</x-slot>

    <div class="max-w-[720px] mx-auto space-y-6">

        <a href="{{ route('links.index') }}" class="inline-flex items-center gap-1 type-body-sm text-slate hover:text-ink-deep">
            <x-heroicon-m-arrow-left class="w-4 h-4"/>
            Quay lại danh sách
        </a>

        <div>
            <div class="section-label mb-3"><span>Liên kết mới</span></div>
            <h1 class="type-display-lg text-ink-deep">Tạo liên kết<br><span class="font-light italic text-slate">trong 10 giây.</span></h1>
            <p class="type-subtitle-md text-charcoal mt-4">Dán URL gốc. Đặt alias nếu muốn dễ nhớ. Sau khi tạo, mỗi click hợp lệ sẽ cộng tiền vào ví của bạn.</p>
        </div>

        <form method="POST" action="{{ route('links.store') }}" class="card-feature !p-8 space-y-6">
            @csrf

            <div>
                <label for="original_url" class="type-body-sm-bold text-ink-deep block mb-2">
                    URL gốc <span class="text-critical">*</span>
                </label>
                <div class="relative">
                    <x-heroicon-o-link class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                    <input id="original_url" name="original_url" value="{{ old('original_url') }}" type="url" required autofocus
                           placeholder="https://example.com/bai-viet-cua-toi"
                           class="input pl-11 @error('original_url') error @enderror"/>
                </div>
                <p class="type-caption text-stone mt-2">Bất kỳ URL nào (http hoặc https). Tối đa 2048 ký tự.</p>
                @error('original_url') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-hairline-soft pt-6">
                <label for="custom_alias" class="type-body-sm-bold text-ink-deep block mb-2 flex items-center justify-between">
                    <span>Alias tuỳ chỉnh</span>
                    <span class="type-caption text-stone font-normal">tuỳ chọn</span>
                </label>
                <div class="flex items-stretch rounded-lg border border-hairline overflow-hidden focus-within:border-[color:var(--color-fb-blue)] focus-within:border-2 @error('custom_alias') !border-critical-strong @enderror">
                    <span class="bg-surface-soft px-4 flex items-center type-body-sm text-slate font-mono">linkpay.vn/</span>
                    <input id="custom_alias" name="custom_alias" value="{{ old('custom_alias') }}" type="text" pattern="[A-Za-z0-9_-]{3,32}"
                           placeholder="ten-de-nho"
                           class="flex-1 px-3 type-body-md outline-none bg-canvas font-mono"/>
                </div>
                <p class="type-caption text-stone mt-2">3-32 ký tự (chữ, số, dấu - hoặc _). Để trống = slug random 6 ký tự.</p>
                @error('custom_alias') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-hairline-soft pt-6">
                <label for="password" class="type-body-sm-bold text-ink-deep block mb-2 flex items-center justify-between">
                    <span class="flex items-center gap-2"><x-heroicon-o-lock-closed class="w-4 h-4"/> Mật khẩu bảo vệ</span>
                    <span class="type-caption text-stone font-normal">tuỳ chọn</span>
                </label>
                <input id="password" name="password" type="text" autocomplete="off"
                       placeholder="Để trống nếu không cần"
                       class="input @error('password') error @enderror"/>
                <p class="type-caption text-stone mt-2">Người xem phải nhập đúng mật khẩu mới mở được link.</p>
                @error('password') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('links.index') }}" class="btn btn-ghost">Huỷ</a>
                <button type="submit" class="btn btn-primary">
                    Tạo liên kết
                    <x-heroicon-m-arrow-right class="w-4 h-4"/>
                </button>
            </div>
        </form>

        {{-- Tip card --}}
        <div class="card-feature !p-6 !bg-primary-soft !border-primary/20">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-canvas flex items-center justify-center text-primary-deep flex-shrink-0">
                    <x-heroicon-s-light-bulb class="w-5 h-5"/>
                </div>
                <div>
                    <h3 class="type-subtitle-lg text-ink-deep">Mẹo kiếm nhiều hơn</h3>
                    <p class="type-body-sm text-charcoal mt-1">
                        Đặt alias dễ nhớ giúp người chia sẻ tin tưởng → click rate cao hơn 2-3x. Ví dụ: <span class="font-mono text-ink-deep">/tai-lieu-ielts</span> thay vì <span class="font-mono text-stone">/xK2pq8</span>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
