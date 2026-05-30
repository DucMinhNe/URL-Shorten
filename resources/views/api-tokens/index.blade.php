<x-app-layout :title="'API'">
    <x-slot name="header">API</x-slot>

    <div class="max-w-[1000px] mx-auto space-y-6">
        <div>
            <div class="section-label mb-3"><span>Dành cho lập trình viên</span></div>
            <h1 class="type-display-lg text-ink-deep">Rút gọn link qua <span class="lp-grad-text">API</span></h1>
            <p class="type-body-md text-slate mt-3 max-w-[620px]">Tạo token và tích hợp LinkPay vào app/bot của bạn. Mỗi link tạo qua API vẫn tính tiền vào ví như bình thường.</p>
        </div>

        {{-- New token reveal --}}
        @if(session('newToken'))
            <div class="card-feature !p-5 !border-success/40" style="background:rgba(16,185,129,.06);">
                <div class="flex items-center gap-2 type-body-sm-bold text-success">
                    <x-heroicon-s-check-circle class="w-5 h-5"/> Token mới — copy ngay, sẽ không hiện lại!
                </div>
                <div class="flex items-stretch gap-2 mt-3">
                    <code class="flex-1 bg-ink-deep text-on-dark rounded-xl px-4 py-3 font-mono text-sm truncate">{{ session('newToken') }}</code>
                    <button type="button" class="lp-btn-grad green" data-copy="{{ session('newToken') }}" data-copy-label="Đã copy token">
                        <x-heroicon-o-clipboard class="w-4 h-4"/> Copy
                    </button>
                </div>
            </div>
        @endif

        {{-- Create token --}}
        <form method="POST" action="{{ route('api-tokens.store') }}" class="card-feature !p-5 flex flex-col sm:flex-row gap-3 sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="type-body-sm-bold text-ink-deep block mb-2">Tên token</label>
                <input name="name" required maxlength="60" placeholder="VD: Telegram bot, n8n workflow"
                       class="input @error('name') error @enderror"/>
                @error('name') <p class="type-caption text-critical mt-1">{{ $message }}</p> @enderror
            </div>
            <button class="lp-btn-grad"><x-heroicon-m-plus class="w-4 h-4"/> Tạo token</button>
        </form>

        {{-- Token list --}}
        <div class="card-feature !p-0 overflow-hidden">
            <div class="p-5 border-b border-hairline-soft">
                <h3 class="type-heading-sm text-ink-deep">Token của bạn ({{ $tokens->count() }})</h3>
            </div>
            @if($tokens->isEmpty())
                <div class="p-10 text-center">
                    <x-heroicon-o-key class="w-10 h-10 text-stone mx-auto"/>
                    <p class="type-body-sm text-slate mt-2">Chưa có token nào. Tạo cái đầu tiên ở trên.</p>
                </div>
            @else
                <div class="divide-y divide-hairline-soft">
                    @foreach($tokens as $t)
                        <div class="px-5 py-4 flex items-center gap-4">
                            <div class="lp-ic lp-ic-violet"><x-heroicon-o-key class="w-5 h-5"/></div>
                            <div class="flex-1 min-w-0">
                                <div class="type-body-sm-bold text-ink-deep truncate">{{ $t->name }}</div>
                                <div class="type-caption text-stone">
                                    <code>{{ substr($t->token, 0, 9) }}…{{ substr($t->token, -4) }}</code>
                                    · {{ $t->last_used_at ? 'Dùng '.$t->last_used_at->diffForHumans() : 'Chưa dùng' }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('api-tokens.destroy', $t) }}" onsubmit="return confirm('Thu hồi token này? App đang dùng sẽ ngừng hoạt động.')">
                                @csrf @method('DELETE')
                                <button class="btn-icon-ghost text-critical hover:bg-[color:var(--color-critical-soft)]" title="Thu hồi">
                                    <x-heroicon-o-trash class="w-4 h-4"/>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Usage example --}}
        <div class="card-feature !p-0 overflow-hidden">
            <div class="p-5 border-b border-hairline-soft flex items-center gap-2">
                <x-heroicon-o-command-line class="w-5 h-5 text-charcoal"/>
                <h3 class="type-heading-sm text-ink-deep">Ví dụ sử dụng</h3>
            </div>
            <pre class="bg-ink-deep text-on-dark p-5 overflow-x-auto text-sm leading-relaxed"><code>curl -X POST {{ url('/api/v1/shorten') }} \
  -H "Authorization: Bearer lp_your_token_here" \
  -H "Content-Type: application/json" \
  -d '{"url":"https://example.com/bai-viet","alias":"bai-hay"}'</code></pre>
            <div class="p-5">
                <div class="type-caption-bold uppercase tracking-wider text-stone mb-2">Phản hồi</div>
                <pre class="bg-surface-soft rounded-xl p-4 overflow-x-auto text-sm text-charcoal"><code>{
  "slug": "bai-hay",
  "short_url": "{{ url('/bai-hay') }}",
  "original_url": "https://example.com/bai-viet"
}</code></pre>
            </div>
        </div>
    </div>
</x-app-layout>
