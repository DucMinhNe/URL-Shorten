<x-app-layout :title="'Hồ sơ'">
    <x-slot name="header">Hồ sơ tài khoản</x-slot>

    <div class="max-w-[720px] mx-auto space-y-6">
        <div>
            <div class="section-label mb-3"><span>Tài khoản</span></div>
            <h1 class="type-display-lg text-ink-deep">Hồ sơ <span class="font-light italic text-slate">của bạn.</span></h1>
            <p class="type-subtitle-md text-charcoal mt-3">Quản lý thông tin cá nhân, mật khẩu và xoá tài khoản nếu cần.</p>
        </div>

        @include('profile.partials.update-profile-information-form')

        @include('profile.partials.update-password-form')

        @include('profile.partials.delete-user-form')
    </div>
</x-app-layout>
