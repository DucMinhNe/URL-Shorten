<section class="card-feature !p-8">
    <div class="flex items-start gap-4 mb-6">
        <div class="w-10 h-10 rounded-xl bg-primary-soft flex items-center justify-center text-primary-deep flex-shrink-0">
            <x-heroicon-o-key class="w-5 h-5"/>
        </div>
        <div class="flex-1">
            <h2 class="type-subtitle-lg text-ink-deep">Đổi mật khẩu</h2>
            <p class="type-body-sm text-slate mt-1">Dùng mật khẩu dài, không trùng với các site khác. Tối thiểu 8 ký tự.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="type-body-sm-bold text-ink-deep block mb-2">Mật khẩu hiện tại</label>
            <div class="relative">
                <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                       class="input pl-11 @error('current_password', 'updatePassword') error @enderror"/>
            </div>
            @error('current_password', 'updatePassword') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password" class="type-body-sm-bold text-ink-deep block mb-2">Mật khẩu mới</label>
            <div class="relative">
                <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                       placeholder="Ít nhất 8 ký tự"
                       class="input pl-11 @error('password', 'updatePassword') error @enderror"/>
            </div>
            @error('password', 'updatePassword') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="type-body-sm-bold text-ink-deep block mb-2">Xác nhận mật khẩu mới</label>
            <div class="relative">
                <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                       class="input pl-11 @error('password_confirmation', 'updatePassword') error @enderror"/>
            </div>
            @error('password_confirmation', 'updatePassword') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-hairline-soft">
            <button type="submit" class="btn btn-primary mt-5">
                <x-heroicon-m-check class="w-4 h-4"/>
                Cập nhật mật khẩu
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="type-body-sm text-success font-bold mt-5">
                    ✓ Đã đổi mật khẩu
                </p>
            @endif
        </div>
    </form>
</section>
