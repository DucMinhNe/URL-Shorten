<x-app-layout :title="'Rút gọn hàng loạt'">
    <x-slot name="header">Rút gọn hàng loạt</x-slot>

    <div class="max-w-[820px] mx-auto space-y-6">
        <div>
            <div class="section-label mb-3"><span>Công cụ</span></div>
            <h1 class="type-display-lg text-ink-deep">Rút gọn <span class="lp-grad-text">nhiều link</span> cùng lúc</h1>
            <p class="type-body-md text-slate mt-3">Dán mỗi URL trên một dòng (tối đa 50 link). Hệ thống tạo slug ngẫu nhiên cho từng cái.</p>
        </div>

        @if($errors->any())
            <div class="card-feature !p-4 !bg-[color:var(--color-critical-soft)] !border-critical/30 type-body-sm-bold text-critical">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('links.bulk-store') }}" class="card-feature !p-6 space-y-4">
            @csrf
            <div>
                <label class="type-body-sm-bold text-ink-deep">Danh sách URL</label>
                <textarea name="urls" rows="9" required
                          class="mt-2 w-full rounded-xl border border-hairline-soft bg-canvas px-4 py-3 font-mono type-body-sm outline-none focus:border-primary"
                          placeholder="https://example.com/bai-viet-1&#10;https://example.com/bai-viet-2&#10;https://shopee.vn/sale">{{ old('urls') }}</textarea>
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route('links.index') }}" class="btn btn-ghost">Huỷ</a>
                <button type="submit" class="lp-btn-grad">
                    <x-heroicon-m-bolt class="w-4 h-4"/>
                    Rút gọn tất cả
                </button>
            </div>
        </form>

        @if(session('bulkResult'))
            @php $r = session('bulkResult'); @endphp
            <div class="card-feature !p-0 overflow-hidden">
                <div class="p-5 border-b border-hairline-soft flex items-center gap-3">
                    <div class="lp-ic lp-ic-green"><x-heroicon-s-check class="w-5 h-5"/></div>
                    <div>
                        <div class="type-heading-sm text-ink-deep">Đã tạo {{ count($r['created']) }} liên kết</div>
                        @if(count($r['failed']))
                            <div class="type-caption text-critical">{{ count($r['failed']) }} URL bị bỏ qua (không hợp lệ hoặc bị chặn)</div>
                        @endif
                    </div>
                    <button type="button" class="lp-btn-grad green ml-auto !py-2 !px-3"
                            data-copy="{{ collect($r['created'])->pluck('short')->implode("\n") }}" data-copy-label="Đã copy tất cả link">
                        <x-heroicon-o-clipboard class="w-4 h-4"/> Copy tất cả
                    </button>
                </div>
                <div class="divide-y divide-hairline-soft">
                    @foreach($r['created'] as $row)
                        <div class="px-5 py-3 flex items-center gap-3">
                            <a href="{{ $row['short'] }}" target="_blank" class="font-mono type-body-sm-bold text-primary truncate">{{ $row['short'] }}</a>
                            <span class="type-caption text-stone truncate flex-1">← {{ $row['url'] }}</span>
                            <button type="button" class="btn-icon-ghost lp-copy" data-copy="{{ $row['short'] }}">
                                <x-heroicon-o-clipboard class="w-4 h-4"/>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
