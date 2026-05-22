<x-app-layout :title="'Sửa liên kết'">
    <x-slot name="header">Sửa liên kết</x-slot>

    <div class="max-w-[720px] mx-auto space-y-6">

        <a href="{{ route('links.index') }}" class="inline-flex items-center gap-1 type-body-sm text-slate hover:text-ink-deep">
            <x-heroicon-m-arrow-left class="w-4 h-4"/>
            Quay lại danh sách
        </a>

        <div>
            <div class="section-label mb-3"><span>Đang sửa</span></div>
            <h1 class="type-display-lg text-ink-deep font-mono">/{{ $link->slug }}</h1>
            <p class="type-subtitle-md text-charcoal mt-3 truncate">→ {{ $link->original_url }}</p>
        </div>

        {{-- Stats card --}}
        <div class="card-feature !p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <div class="type-caption-bold uppercase tracking-wider text-stone">Click</div>
                <div class="type-heading-sm text-ink-deep mt-1">{{ number_format($link->total_clicks) }}</div>
            </div>
            <div>
                <div class="type-caption-bold uppercase tracking-wider text-stone">View hợp lệ</div>
                <div class="type-heading-sm text-ink-deep mt-1">{{ number_format($link->valid_views) }}</div>
            </div>
            <div>
                <div class="type-caption-bold uppercase tracking-wider text-stone">Doanh thu</div>
                <div class="type-heading-sm text-success mt-1">{{ number_format($link->total_earned) }}đ</div>
            </div>
            <div>
                <div class="type-caption-bold uppercase tracking-wider text-stone">Valid rate</div>
                <div class="type-heading-sm text-ink-deep mt-1">
                    {{ $link->total_clicks > 0 ? number_format($link->valid_views / $link->total_clicks * 100, 1) : 0 }}%
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('links.update', $link) }}" class="card-feature !p-8 space-y-6">
            @csrf @method('PUT')

            <div>
                <label for="original_url" class="type-body-sm-bold text-ink-deep block mb-2">URL gốc</label>
                <div class="relative">
                    <x-heroicon-o-link class="w-5 h-5 text-steel absolute left-4 top-1/2 -translate-y-1/2"/>
                    <input id="original_url" name="original_url" value="{{ old('original_url', $link->original_url) }}" type="url" required
                           class="input pl-11 @error('original_url') error @enderror"/>
                </div>
                @error('original_url') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-hairline-soft pt-6">
                <label class="type-body-sm-bold text-ink-deep block mb-2">Alias <span class="type-caption text-stone font-normal ml-2">không thể đổi</span></label>
                <div class="flex items-stretch rounded-lg border border-hairline overflow-hidden opacity-60">
                    <span class="bg-surface-soft px-4 flex items-center type-body-sm text-slate font-mono">linkpay.vn/</span>
                    <input value="{{ $link->slug }}" disabled type="text"
                           class="flex-1 px-3 type-body-md outline-none bg-canvas font-mono"/>
                </div>
            </div>

            <div class="border-t border-hairline-soft pt-6">
                <label for="status" class="type-body-sm-bold text-ink-deep block mb-2">Trạng thái</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-4 rounded-lg border-2 cursor-pointer {{ old('status', $link->status) === 'active' ? 'border-primary bg-primary-soft' : 'border-hairline-soft hover:border-hairline' }}">
                        <input type="radio" name="status" value="active" {{ old('status', $link->status) === 'active' ? 'checked' : '' }} class="text-primary"/>
                        <div>
                            <div class="type-body-sm-bold text-ink-deep">Hoạt động</div>
                            <div class="type-caption text-stone">Đang kiếm tiền</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-lg border-2 cursor-pointer {{ old('status', $link->status) === 'disabled' ? 'border-primary bg-primary-soft' : 'border-hairline-soft hover:border-hairline' }}">
                        <input type="radio" name="status" value="disabled" {{ old('status', $link->status) === 'disabled' ? 'checked' : '' }} class="text-primary"/>
                        <div>
                            <div class="type-body-sm-bold text-ink-deep">Đã tắt</div>
                            <div class="type-caption text-stone">Người click sẽ thấy 410</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="border-t border-hairline-soft pt-6">
                <label for="password" class="type-body-sm-bold text-ink-deep block mb-2">
                    @if($link->password)
                        Đổi mật khẩu <span class="badge badge-info ml-2 !text-xs">Đang bảo vệ</span>
                    @else
                        Thêm mật khẩu
                    @endif
                </label>
                <input id="password" name="password" type="text" autocomplete="off"
                       placeholder="{{ $link->password ? 'Nhập mật khẩu mới (để trống = giữ nguyên)' : 'Để trống nếu không cần' }}"
                       class="input @error('password') error @enderror"/>
                @if($link->password)
                    <label class="flex items-center gap-2 mt-3 type-body-sm text-slate cursor-pointer">
                        <input type="checkbox" name="remove_password" value="1" class="rounded border-hairline text-primary"/>
                        Xoá mật khẩu (link sẽ mở công khai)
                    </label>
                @endif
                @error('password') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-hairline-soft -mx-8 px-8 -mb-8 pb-8 bg-surface-soft mt-8 rounded-b-[32px]">
                <a href="{{ route('links.index') }}" class="btn btn-ghost">Huỷ</a>
                <button type="submit" class="btn btn-primary">
                    <x-heroicon-m-check class="w-4 h-4"/>
                    Lưu thay đổi
                </button>
            </div>
        </form>

        {{-- Danger zone --}}
        <div class="card-feature !p-6 !border-critical/20">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-[color:var(--color-critical-soft)] flex items-center justify-center text-critical flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5"/>
                </div>
                <div class="flex-1">
                    <h3 class="type-subtitle-lg text-critical">Vùng nguy hiểm</h3>
                    <p class="type-body-sm text-slate mt-1">Xoá liên kết sẽ mất toàn bộ thống kê click và doanh thu lịch sử. Không thể hoàn tác.</p>
                </div>
                <form method="POST" action="{{ route('links.destroy', $link) }}" onsubmit="return confirm('Xoá vĩnh viễn /{{ $link->slug }}? Hành động không thể hoàn tác.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-critical-ghost">
                        <x-heroicon-o-trash class="w-4 h-4"/>
                        Xoá liên kết
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
