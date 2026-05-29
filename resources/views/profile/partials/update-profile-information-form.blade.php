<section class="card-feature !p-8">
    <div class="flex items-start gap-4 mb-6">
        <div class="w-10 h-10 rounded-xl bg-primary-soft flex items-center justify-center text-primary-deep flex-shrink-0">
            <x-heroicon-o-user class="w-5 h-5"/>
        </div>
        <div class="flex-1">
            <h2 class="type-subtitle-lg text-ink-deep">Thông tin cá nhân</h2>
            <p class="type-body-sm text-slate mt-1">Tên hiển thị và email đăng nhập. Đổi email sẽ phải xác minh lại.</p>
        </div>
    </div>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="type-body-sm-bold text-ink-deep block mb-2">Tên hiển thị</label>
            <div class="relative">
                <x-heroicon-o-user class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                <input id="name" name="name" type="text" required autofocus autocomplete="name"
                       value="{{ old('name', $user->name) }}"
                       class="input pl-11 @error('name') error @enderror"/>
            </div>
            @error('name') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="type-body-sm-bold text-ink-deep block mb-2">Email</label>
            <div class="relative">
                <x-heroicon-o-envelope class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                <input id="email" name="email" type="email" required autocomplete="username"
                       value="{{ old('email', $user->email) }}"
                       class="input pl-11 @error('email') error @enderror"/>
            </div>
            @error('email') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-lg bg-[color:var(--color-warning-soft)] flex items-start gap-3">
                    <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-[color:var(--color-warning)] flex-shrink-0 mt-0.5"/>
                    <div class="flex-1">
                        <p class="type-body-sm-bold text-ink-deep">Email chưa xác minh.</p>
                        <button form="send-verification" class="type-body-sm text-primary-deep font-bold underline underline-offset-2 mt-1">
                            Gửi lại email xác minh
                        </button>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 type-body-sm text-success font-bold">
                        Link xác minh mới đã được gửi tới email của bạn.
                    </p>
                @endif
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-hairline-soft">
            <button type="submit" class="btn btn-primary mt-5">
                <x-heroicon-m-check class="w-4 h-4"/>
                Lưu thay đổi
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="type-body-sm text-success font-bold mt-5">
                    ✓ Đã lưu
                </p>
            @endif
        </div>
    </form>
</section>
