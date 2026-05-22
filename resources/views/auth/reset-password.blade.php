<x-guest-layout :title="'Đặt mật khẩu mới — LinkPay'">
<div class="min-h-screen flex flex-col">
    <header class="px-8 py-6 flex items-center justify-between border-b border-hairline-soft">
        <x-brand size="md"/>
    </header>

    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[480px]">
            <div class="card-feature">
                <div class="w-16 h-16 rounded-full bg-primary-soft flex items-center justify-center text-primary-deep mb-6">
                    <x-heroicon-o-lock-closed class="w-7 h-7"/>
                </div>

                <div class="section-label mb-3"><span>Tạo mật khẩu mới</span></div>
                <h1 class="type-heading-lg text-ink-deep">Mật khẩu mới<br><span class="font-light italic text-slate">cho lần đăng nhập sau.</span></h1>

                <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="type-body-sm-bold text-ink-deep block mb-2">Email</label>
                        <div class="relative">
                            <x-heroicon-o-envelope class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus
                                   class="input pl-11 @error('email') error @enderror"/>
                        </div>
                        @error('email') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="type-body-sm-bold text-ink-deep block mb-2">Mật khẩu mới</label>
                        <div class="relative">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                            <input id="password" name="password" type="password" required
                                   placeholder="Ít nhất 8 ký tự"
                                   class="input pl-11 @error('password') error @enderror"/>
                        </div>
                        @error('password') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="type-body-sm-bold text-ink-deep block mb-2">Xác nhận mật khẩu</label>
                        <div class="relative">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="input pl-11"/>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        Đặt mật khẩu mới
                        <x-heroicon-m-check class="w-4 h-4"/>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
