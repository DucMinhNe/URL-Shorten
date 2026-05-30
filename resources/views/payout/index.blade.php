<x-app-layout :title="'Rút tiền'">
    <x-slot name="header">Rút tiền</x-slot>

    @php
        $user = auth()->user();
        $totalPaid = $user->payoutRequests()->where('status','paid')->sum('amount');
        $pending = $user->payoutRequests()->whereIn('status', ['pending','approved'])->sum('amount');
    @endphp

    <div class="max-w-[1280px] mx-auto space-y-6">
        <div>
            <div class="section-label mb-3"><span>Ví & thanh toán</span></div>
            <h1 class="type-display-lg text-ink-deep">Rút tiền<br><span class="font-light italic text-slate">về tài khoản của bạn.</span></h1>
        </div>

        {{-- Balance hero --}}
        <div class="card-promo-dark relative overflow-hidden !p-10">
            <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-primary opacity-20 blur-3xl"></div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative">
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-[color:var(--color-warning)] pulse-dot"></span>
                        <span class="type-caption-bold uppercase tracking-wider text-stone">Số dư khả dụng</span>
                    </div>
                    <div class="type-hero-display text-on-dark">{{ number_format($user->balance) }}<span class="type-display-lg ml-2">đ</span></div>
                    <p class="type-body-md text-stone mt-3">
                        ≈ ${{ number_format($user->balance / 25000, 2) }} USD · Quy đổi tỷ giá 25.000đ/$1
                    </p>
                </div>
                <div class="lg:col-span-5 grid grid-cols-3 gap-4 content-start">
                    <div>
                        <div class="type-caption-bold uppercase tracking-wider text-stone">Đã kiếm</div>
                        <div class="type-heading-sm text-on-dark mt-1">{{ number_format($user->total_earned) }}đ</div>
                    </div>
                    <div>
                        <div class="type-caption-bold uppercase tracking-wider text-stone">Đã rút</div>
                        <div class="type-heading-sm text-on-dark mt-1">{{ number_format($totalPaid) }}đ</div>
                    </div>
                    <div>
                        <div class="type-caption-bold uppercase tracking-wider text-stone">Đang chờ</div>
                        <div class="type-heading-sm text-[color:var(--color-warning)] mt-1">{{ number_format($pending) }}đ</div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="card-feature !p-4 !bg-[color:var(--color-success-soft)] !border-success/30 flex items-center gap-3">
                <x-heroicon-s-check-circle class="w-5 h-5 text-success"/>
                <span class="type-body-sm-bold text-success">{{ session('status') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Request form --}}
            <div class="lg:col-span-2">
                @php $min = 100000; @endphp
                @if($user->balance < $min)
                <div class="card-feature !p-8 space-y-4 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-[color:var(--color-warning-soft)] flex items-center justify-center">
                        <x-heroicon-o-banknotes class="w-7 h-7 text-[color:var(--color-warning)]"/>
                    </div>
                    <div>
                        <div class="section-label justify-center mb-2"><span>Chưa đủ điều kiện</span></div>
                        <h2 class="type-heading-sm text-ink-deep">Chưa thể rút tiền</h2>
                        <p class="type-body-sm text-slate mt-2">Cần tối thiểu 100.000đ để rút — còn thiếu {{ number_format($min - $user->balance) }}đ. Tạo thêm liên kết để kiếm tiền.</p>
                    </div>
                    <a href="{{ route('links.create') }}" class="btn btn-buy w-full">
                        Tạo liên kết mới
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                </div>
                @else
                <form method="POST" action="{{ route('payout.store') }}" class="card-feature !p-8 space-y-6">
                    @csrf
                    <div>
                        <div class="section-label mb-2"><span>Yêu cầu mới</span></div>
                        <h2 class="type-heading-sm text-ink-deep">Rút tiền</h2>
                        <p class="type-body-sm text-slate mt-1">Admin duyệt và chuyển trong vòng 24h.</p>
                    </div>

                    <div>
                        <label for="amount" class="type-body-sm-bold text-ink-deep block mb-2">Số tiền</label>
                        <div class="relative">
                            <input id="amount" name="amount" type="number" min="100000" max="{{ $user->balance }}" value="{{ old('amount') }}" required
                                   placeholder="100.000"
                                   class="input pr-12 type-heading-sm font-bold text-right @error('amount') error @enderror"/>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 type-body-md-bold text-slate">đ</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach([100000 => '100k', 200000 => '200k', 500000 => '500k'] as $qfVal => $qfLabel)
                                @if($qfVal <= $user->balance)
                                    <button type="button" onclick="document.getElementById('amount').value={{ $qfVal }}" class="pill-tab !py-1 !px-3 type-caption-bold">{{ $qfLabel }}</button>
                                @endif
                            @endforeach
                            <button type="button" onclick="document.getElementById('amount').value={{ $user->balance }}" class="pill-tab !py-1 !px-3 type-caption-bold">Max ({{ number_format($user->balance) }}đ)</button>
                        </div>
                        @error('amount') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="type-body-sm-bold text-ink-deep block mb-2">Phương thức</label>
                        <div class="space-y-2">
                            @php
                                $methods = [
                                    ['key'=>'momo',   'name'=>'MoMo',    'desc'=>'SĐT MoMo · Min 100.000đ',     'logo'=>'momo.svg',    'bg'=>'#ea27c2'],
                                    ['key'=>'zalo',   'name'=>'ZaloPay', 'desc'=>'SĐT ZaloPay · Min 100.000đ', 'logo'=>'zalopay.svg', 'bg'=>'#0068FF'],
                                    ['key'=>'paypal', 'name'=>'PayPal',  'desc'=>'Email PayPal · Min $4 USD',  'logo'=>'paypal.svg',  'bg'=>'#003087'],
                                ];
                                $checked = old('method', $user->payout_method ?? 'momo');
                            @endphp
                            @foreach($methods as $m)
                                <label class="flex items-center gap-3 p-4 rounded-lg border-2 cursor-pointer transition-colors {{ $checked === $m['key'] ? 'border-primary bg-primary-soft' : 'border-hairline-soft hover:border-hairline' }}">
                                    <input type="radio" name="method" value="{{ $m['key'] }}" {{ $checked === $m['key'] ? 'checked' : '' }} class="text-primary"/>
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 p-1.5" style="background: {{ $m['bg'] }};">
                                        <img src="{{ asset('images/payment/'.$m['logo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-contain {{ $m['key'] === 'paypal' ? 'brightness-0 invert' : '' }}">
                                    </div>
                                    <div class="flex-1">
                                        <div class="type-body-sm-bold text-ink-deep">{{ $m['name'] }}</div>
                                        <div class="type-caption text-stone">{{ $m['desc'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="account_info" class="type-body-sm-bold text-ink-deep block mb-2">Thông tin nhận tiền</label>
                        <input id="account_info" name="account_info" type="text" value="{{ old('account_info', $user->payout_account) }}" required
                               placeholder="0901234567 hoặc email@paypal.com"
                               class="input @error('account_info') error @enderror"/>
                        <p class="type-caption text-stone mt-2">SĐT Momo/Zalo (10 số) hoặc email PayPal.</p>
                        @error('account_info') <p class="type-body-sm text-critical mt-2">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-buy w-full">
                        Gửi yêu cầu rút tiền
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </button>

                    <p class="type-caption text-stone text-center">
                        Bằng việc gửi yêu cầu, bạn đồng ý <a href="{{ route('faq') }}" class="text-ink-deep font-bold underline">điều khoản rút tiền</a>.
                    </p>
                </form>
                @endif
            </div>

            {{-- History --}}
            <div class="lg:col-span-3 card-feature !p-0 overflow-hidden">
                <div class="p-6 flex items-center justify-between border-b border-hairline-soft flex-wrap gap-3">
                    <div>
                        <div class="section-label mb-1"><span>Lịch sử</span></div>
                        <h3 class="type-heading-sm">{{ $requests->total() }} yêu cầu</h3>
                    </div>
                    @php $currentStatus = request('status', 'all'); @endphp
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['all' => 'Tất cả', 'pending' => 'Đang chờ', 'paid' => 'Đã chuyển', 'rejected' => 'Từ chối'] as $key => $label)
                            <a href="{{ route('payout.index', $key === 'all' ? [] : ['status' => $key]) }}"
                               class="pill-tab !py-1.5 !px-3 type-caption-bold {{ $currentStatus === $key ? 'active' : '' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($requests->isEmpty())
                    <div class="p-12 text-center">
                        <x-heroicon-o-banknotes class="w-12 h-12 text-stone mx-auto"/>
                        <p class="type-body-md text-slate mt-3">Chưa có yêu cầu rút tiền nào.</p>
                    </div>
                @else
                    <div class="divide-y divide-hairline-soft">
                        @foreach($requests as $r)
                            @php
                                $statusBadge = ['pending'=>'badge-warning','approved'=>'badge-info','paid'=>'badge-success','rejected'=>'badge-critical'][$r->status] ?? 'badge-neutral';
                                $statusLabel = ['pending'=>'Đang chờ','approved'=>'Đã duyệt','paid'=>'Đã chuyển','rejected'=>'Từ chối'][$r->status] ?? $r->status;
                                $methodMeta = [
                                    'momo'   => ['logo'=>'momo.svg',    'bg'=>'#ea27c2', 'invert'=>false],
                                    'zalo'   => ['logo'=>'zalopay.svg', 'bg'=>'#0068FF', 'invert'=>false],
                                    'paypal' => ['logo'=>'paypal.svg',  'bg'=>'#003087', 'invert'=>true],
                                ][$r->method] ?? ['logo'=>'momo.svg','bg'=>'#999','invert'=>false];
                            @endphp
                            <details class="group">
                                <summary class="p-5 cursor-pointer flex items-center gap-4 list-none hover:bg-surface-soft transition-colors">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 p-1.5" style="background: {{ $methodMeta['bg'] }};">
                                        <img src="{{ asset('images/payment/'.$methodMeta['logo']) }}" alt="{{ $r->method }}" class="w-full h-full object-contain {{ $methodMeta['invert'] ? 'brightness-0 invert' : '' }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="type-body-md-bold text-ink-deep">{{ number_format($r->amount) }}đ</div>
                                        <div class="type-caption text-stone">{{ ucfirst($r->method) }} · {{ $r->account_info }}</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                                        <div class="type-caption text-stone mt-1">{{ $r->created_at->diffForHumans() }}</div>
                                    </div>
                                    <x-heroicon-o-chevron-down class="w-4 h-4 text-stone group-open:rotate-180 transition-transform"/>
                                </summary>
                                <div class="px-5 pb-5 pt-2 bg-surface-soft type-body-sm space-y-2">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <span class="text-stone">Tạo lúc:</span>
                                            <span class="text-ink-deep">{{ $r->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if($r->processed_at)
                                            <div>
                                                <span class="text-stone">Xử lý lúc:</span>
                                                <span class="text-ink-deep">{{ $r->processed_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($r->transaction_ref)
                                            <div class="col-span-2">
                                                <span class="text-stone">Mã giao dịch:</span>
                                                <span class="font-mono text-ink-deep">{{ $r->transaction_ref }}</span>
                                            </div>
                                        @endif
                                        @if($r->admin_note)
                                            <div class="col-span-2 mt-2 p-3 bg-canvas rounded-lg border-l-4 border-critical">
                                                <div class="type-caption-bold text-critical uppercase tracking-wider mb-1">Lý do từ chối</div>
                                                <div class="text-charcoal">{{ $r->admin_note }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif

                @if($requests->hasPages())
                    <div class="p-4 border-t border-hairline-soft">{{ $requests->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
