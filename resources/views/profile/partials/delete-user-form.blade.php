<section x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }" class="card-feature !p-6 !border-critical/20">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-[color:var(--color-critical-soft)] flex items-center justify-center text-critical flex-shrink-0">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5"/>
        </div>
        <div class="flex-1">
            <h3 class="type-subtitle-lg text-critical">Xoá tài khoản</h3>
            <p class="type-body-sm text-slate mt-1">
                Toàn bộ link, click, doanh thu và số dư ví sẽ bị xoá vĩnh viễn. Hãy rút tiền trước khi xoá.
            </p>
        </div>
        <button type="button" x-on:click="open = true" class="btn btn-critical-ghost flex-shrink-0">
            <x-heroicon-o-trash class="w-4 h-4"/>
            Xoá tài khoản
        </button>
    </div>

    {{-- Confirm modal --}}
    <div x-show="open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-deep/60 backdrop-blur-sm"
         x-on:keydown.escape.window="open = false">
        <div x-show="open" x-transition
             x-on:click.outside="open = false"
             class="w-full max-w-[480px] bg-canvas rounded-3xl p-8 shadow-2xl">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-[color:var(--color-critical-soft)] flex items-center justify-center text-critical flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6"/>
                </div>
                <div>
                    <h3 class="type-heading-sm text-ink-deep">Xác nhận xoá tài khoản</h3>
                    <p class="type-body-sm text-slate mt-1">Hành động không thể hoàn tác. Nhập mật khẩu để xác nhận.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div>
                    <label for="password" class="type-body-sm-bold text-ink-deep block mb-2">Mật khẩu hiện tại</label>
                    <div class="relative">
                        <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                        <input id="password" name="password" type="password" required autofocus
                               placeholder="Nhập mật khẩu để xác nhận"
                               class="input pl-11 @error('password', 'userDeletion') error @enderror"/>
                    </div>
                    @error('password', 'userDeletion') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-hairline-soft">
                    <button type="button" x-on:click="open = false" class="btn btn-ghost">Huỷ</button>
                    <button type="submit" class="btn btn-critical">
                        <x-heroicon-o-trash class="w-4 h-4"/>
                        Xoá vĩnh viễn
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
