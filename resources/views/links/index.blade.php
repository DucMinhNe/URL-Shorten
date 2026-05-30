<x-app-layout :title="'Liên kết của tôi'">
    <x-slot name="header">Liên kết của tôi</x-slot>

    <div class="max-w-[1400px] mx-auto space-y-6"
         x-data="{
            qrOpen: false, qrImg: '', qrPng: '', qrSvg: '', qrUrl: '',
            selected: [],
            allOnPage: @js($links->getCollection()->pluck('id')->all()),
            toggleAll(e) { this.selected = e.target.checked ? [...this.allOnPage] : []; },
            isAll() { return this.allOnPage.length && this.selected.length === this.allOnPage.length; },
            runBulk(action) {
                if (!this.selected.length) return;
                if (action === 'delete' && !confirm('Xoá ' + this.selected.length + ' liên kết đã chọn? Thống kê click sẽ mất.')) return;
                const f = document.getElementById('bulkForm');
                f.querySelector('[name=action]').value = action;
                f.querySelector('.ids').innerHTML = this.selected.map(id => '<input type=hidden name=\'ids[]\' value=\'' + id + '\'>').join('');
                f.submit();
            }
         }">

        {{-- Hidden bulk form --}}
        <form id="bulkForm" method="POST" action="{{ route('links.bulk') }}" class="hidden">
            @csrf
            <input type="hidden" name="action" value="">
            <span class="ids"></span>
        </form>

        {{-- Header --}}
        <div class="flex items-end justify-between flex-wrap gap-4">
            <div>
                <div class="section-label mb-3"><span>Quản lý liên kết</span></div>
                <h1 class="type-display-lg text-ink-deep">Tất cả liên kết<br><span class="font-light italic text-slate">của bạn.</span></h1>
                <p class="type-body-md text-slate mt-3">
                    {{ $links->total() }} liên kết · {{ number_format(auth()->user()->shortLinks()->sum('total_clicks')) }} click · {{ number_format(auth()->user()->shortLinks()->sum('total_earned')) }}đ doanh thu
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('links.export') }}" class="btn btn-ghost" title="Tải CSV">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4"/>
                    <span class="hidden sm:inline">Export CSV</span>
                </a>
                <a href="{{ route('links.bulk-create') }}" class="btn btn-ghost">
                    <x-heroicon-o-queue-list class="w-4 h-4"/>
                    <span class="hidden sm:inline">Rút gọn hàng loạt</span>
                </a>
                <a href="{{ route('links.create') }}" class="lp-btn-grad">
                    <x-heroicon-m-plus class="w-4 h-4"/>
                    Tạo liên kết mới
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('shortUrl'))
            <div class="card-feature !p-5 !bg-[color:var(--color-success-soft)] !border-success/30 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-success text-on-dark flex items-center justify-center flex-shrink-0">
                    <x-heroicon-s-check class="w-5 h-5"/>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="type-body-sm-bold text-success">Đã tạo liên kết thành công</div>
                    <a href="{{ session('shortUrl') }}" class="font-mono type-body-sm text-ink-deep truncate block" target="_blank">{{ session('shortUrl') }}</a>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ session('shortUrl') }}'); this.innerHTML='Đã copy ✓'" class="btn btn-ghost !py-2">
                    <x-heroicon-o-clipboard class="w-4 h-4"/>
                    Copy
                </button>
            </div>
        @endif
        @if(session('status'))
            <div class="card-feature !p-4 !bg-[color:var(--color-success-soft)] !border-success/30 type-body-sm-bold text-success">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filter bar --}}
        @php
            $currentStatus = request('status', 'all');
            $currentSort = request('sort', 'latest');
            $currentQ = trim((string) request('q', ''));
            $sortLabels = ['latest' => 'Mới nhất', 'clicks' => 'Click nhiều', 'earnings' => 'Doanh thu cao'];
        @endphp
        <form method="GET" action="{{ route('links.index') }}" class="card-feature !p-3 !rounded-2xl flex flex-wrap items-center gap-3"
              x-data="{ open: false }">
            <div class="flex items-center gap-2 search-pill !flex-1 !min-w-[200px] max-w-[320px]">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-steel ml-2"/>
                <input type="text" name="q" value="{{ $currentQ }}"
                       placeholder="Tìm theo slug hoặc URL..."
                       class="bg-transparent border-0 outline-none flex-1 type-body-sm"/>
                @if($currentQ !== '')
                    <a href="{{ route('links.index') }}" class="btn-icon-ghost mr-1" title="Xoá tìm kiếm">
                        <x-heroicon-m-x-mark class="w-4 h-4"/>
                    </a>
                @endif
            </div>

            <input type="hidden" name="sort" value="{{ $currentSort }}">

            <div class="flex items-center gap-2 flex-wrap">
                @foreach(['all' => 'Tất cả', 'active' => 'Hoạt động', 'disabled' => 'Đã tắt'] as $key => $label)
                    @php $url = route('links.index', array_filter(['q' => $currentQ ?: null, 'status' => $key === 'all' ? null : $key, 'sort' => $currentSort !== 'latest' ? $currentSort : null])); @endphp
                    <a href="{{ $url }}" class="pill-tab !py-1.5 !px-3 type-caption-bold {{ $currentStatus === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="ml-auto relative" x-data="{ open: false }">
                <button type="button" x-on:click="open = !open" class="pill-tab !py-1.5 !px-3 type-caption-bold">
                    <x-heroicon-o-arrows-up-down class="w-3.5 h-3.5"/>
                    {{ $sortLabels[$currentSort] ?? 'Sắp xếp' }}
                </button>
                <div x-show="open" x-cloak x-transition x-on:click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-canvas rounded-xl border border-hairline-soft shadow-lg z-10 overflow-hidden">
                    @foreach($sortLabels as $key => $label)
                        @php $url = route('links.index', array_filter(['q' => $currentQ ?: null, 'status' => $currentStatus !== 'all' ? $currentStatus : null, 'sort' => $key !== 'latest' ? $key : null])); @endphp
                        <a href="{{ $url }}" class="block px-4 py-2 type-body-sm hover:bg-surface-soft {{ $currentSort === $key ? 'text-primary-deep font-bold bg-primary-soft' : 'text-charcoal' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <noscript>
                <button type="submit" class="btn btn-primary !py-1.5">Lọc</button>
            </noscript>
        </form>

        {{-- Tag filter strip --}}
        @if($userTags->isNotEmpty())
            @php $currentTag = trim((string) request('tag', '')); @endphp
            <div class="flex items-center gap-2 flex-wrap">
                <span class="type-caption-bold uppercase tracking-wider text-stone mr-1">Nhãn:</span>
                <a href="{{ route('links.index', array_filter(['q' => $currentQ ?: null, 'status' => $currentStatus !== 'all' ? $currentStatus : null, 'sort' => $currentSort !== 'latest' ? $currentSort : null])) }}"
                   class="lp-tag lp-tag-slate {{ $currentTag === '' ? 'on' : '' }}">Tất cả</a>
                @foreach($userTags as $tag)
                    <a href="{{ route('links.index', ['tag' => $tag->slug]) }}"
                       class="lp-tag lp-tag-{{ $tag->color }} {{ $currentTag === $tag->slug ? 'on' : '' }}">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        {{-- Table --}}
        <div class="card-feature !p-0 overflow-hidden">
            @if($links->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-20 h-20 mx-auto rounded-full bg-surface-soft flex items-center justify-center">
                        <x-heroicon-o-link class="w-10 h-10 text-stone"/>
                    </div>
                    <h3 class="type-heading-sm text-ink-deep mt-6">Chưa có liên kết nào</h3>
                    <p class="type-body-md text-slate mt-2 max-w-[400px] mx-auto">Tạo liên kết đầu tiên để bắt đầu kiếm tiền từ mỗi click.</p>
                    <a href="{{ route('links.create') }}" class="btn btn-primary mt-6">
                        <x-heroicon-m-plus class="w-4 h-4"/>
                        Tạo liên kết đầu tiên
                    </a>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr class="type-caption-bold uppercase tracking-wider text-stone">
                            <th class="pl-6 pr-2 py-3 w-10">
                                <input type="checkbox" class="lp-check" x-on:change="toggleAll($event)" :checked="isAll()" title="Chọn tất cả">
                            </th>
                            <th class="text-left px-2 py-3">Liên kết ngắn</th>
                            <th class="text-left px-6 py-3 hidden md:table-cell">URL gốc</th>
                            <th class="text-right px-6 py-3 hidden sm:table-cell">Click</th>
                            <th class="text-right px-6 py-3 hidden lg:table-cell">View hợp lệ</th>
                            <th class="text-right px-6 py-3">Doanh thu</th>
                            <th class="text-center px-6 py-3 hidden md:table-cell">Trạng thái</th>
                            <th class="w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($links as $link)
                            @php
                                $shortUrl = url('/'.$link->slug);
                                $dStatus = $link->displayStatus();
                                $statusBadge = ['active'=>'badge-success','disabled'=>'badge-neutral','blocked'=>'badge-critical','expired'=>'badge-warning','limit_reached'=>'badge-warning'][$dStatus] ?? 'badge-neutral';
                                $statusLabel = ['active'=>'Hoạt động','disabled'=>'Đã tắt','blocked'=>'Bị chặn','expired'=>'Hết hạn','limit_reached'=>'Đạt giới hạn'][$dStatus] ?? $dStatus;
                                $validRate = $link->total_clicks > 0 ? round($link->valid_views / $link->total_clicks * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-surface-soft transition-colors group" :class="selected.includes({{ $link->id }}) && 'bg-primary-soft/40'">
                                <td class="pl-6 pr-2 py-4">
                                    <input type="checkbox" class="lp-check" value="{{ $link->id }}" x-model.number="selected">
                                </td>
                                <td class="px-2 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-surface-soft flex items-center justify-center flex-shrink-0">
                                            @if($link->password)
                                                <x-heroicon-o-lock-closed class="w-4 h-4 text-charcoal"/>
                                            @else
                                                <x-heroicon-o-link class="w-4 h-4 text-charcoal"/>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ $shortUrl }}" target="_blank" class="font-mono type-body-sm-bold text-ink-deep hover:text-primary truncate block">/{{ $link->slug }}</a>
                                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="type-caption text-stone">{{ $link->created_at->diffForHumans() }}</span>
                                                @foreach($link->tags as $tag)
                                                    <a href="{{ route('links.index', ['tag' => $tag->slug]) }}" class="lp-tag lp-tag-{{ $tag->color }}">#{{ $tag->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="type-body-sm text-charcoal truncate max-w-[280px]" title="{{ $link->original_url }}">{{ $link->original_url }}</div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell text-right">
                                    <div class="type-body-sm-bold text-ink-deep">{{ number_format($link->total_clicks) }}</div>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell text-right">
                                    <div class="type-body-sm-bold text-ink-deep">{{ number_format($link->valid_views) }}</div>
                                    <div class="type-caption text-stone">{{ $validRate }}%</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="type-body-sm-bold text-success">{{ number_format($link->total_earned) }}đ</div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-center">
                                    <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button"
                                                x-data="{ copied: false }"
                                                x-on:click="navigator.clipboard.writeText('{{ $shortUrl }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                                class="btn-icon-ghost" :class="copied && 'text-success'" title="Copy short URL">
                                            <template x-if="!copied"><x-heroicon-o-clipboard class="w-4 h-4"/></template>
                                            <template x-if="copied"><x-heroicon-o-check class="w-4 h-4"/></template>
                                        </button>
                                        <button type="button"
                                                x-on:click="qrImg='{{ route('links.qr', $link) }}'; qrPng='{{ route('links.qr', ['link' => $link, 'download' => 1]) }}'; qrSvg='{{ route('links.qr', ['link' => $link, 'format' => 'svg', 'download' => 1]) }}'; qrUrl='{{ $shortUrl }}'; qrOpen=true"
                                                class="btn-icon-ghost" title="QR code">
                                            <x-heroicon-o-qr-code class="w-4 h-4"/>
                                        </button>
                                        <a href="{{ route('links.stats', $link) }}" class="btn-icon-ghost" title="Thống kê">
                                            <x-heroicon-o-chart-bar class="w-4 h-4"/>
                                        </a>
                                        <a href="{{ route('links.edit', $link) }}" class="btn-icon-ghost" title="Sửa">
                                            <x-heroicon-o-pencil-square class="w-4 h-4"/>
                                        </a>
                                        <form method="POST" action="{{ route('links.clone', $link) }}" class="inline">
                                            @csrf
                                            <button class="btn-icon-ghost" title="Nhân bản">
                                                <x-heroicon-o-document-duplicate class="w-4 h-4"/>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('links.destroy', $link) }}" class="inline" onsubmit="return confirm('Xoá liên kết này? Toàn bộ thống kê click sẽ mất.')">
                                            @csrf @method('DELETE')
                                            <button class="btn-icon-ghost text-critical hover:bg-[color:var(--color-critical-soft)]" title="Xoá">
                                                <x-heroicon-o-trash class="w-4 h-4"/>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($links->hasPages())
            <div>{{ $links->links() }}</div>
        @endif

        {{-- Floating bulk-action bar --}}
        <div class="lp-bulkbar" :class="selected.length && 'show'" x-cloak>
            <span class="cnt" x-text="selected.length + ' đã chọn'"></span>
            <button type="button" x-on:click="selected = []" class="!bg-transparent !px-2" title="Bỏ chọn">
                <x-heroicon-m-x-mark class="w-4 h-4"/>
            </button>
            <span class="sep"></span>
            <button type="button" class="ok" x-on:click="runBulk('activate')">
                <x-heroicon-m-check-circle class="w-4 h-4"/> Bật
            </button>
            <button type="button" x-on:click="runBulk('disable')">
                <x-heroicon-m-pause-circle class="w-4 h-4"/> Tắt
            </button>
            <button type="button" class="danger" x-on:click="runBulk('delete')">
                <x-heroicon-m-trash class="w-4 h-4"/> Xoá
            </button>
        </div>

        {{-- Shared QR modal --}}
        <div x-show="qrOpen" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-deep/60 backdrop-blur-sm"
             x-on:click="qrOpen = false" x-on:keydown.escape.window="qrOpen = false">
            <div class="bg-canvas rounded-3xl border border-hairline-soft shadow-xl max-w-[400px] w-full p-8 text-center" x-on:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="type-heading-sm text-ink-deep">QR code</h3>
                    <button type="button" x-on:click="qrOpen = false" class="btn-icon-ghost"><x-heroicon-m-x-mark class="w-5 h-5"/></button>
                </div>
                <div class="rounded-2xl border border-hairline-soft p-4 bg-white inline-block">
                    <img :src="qrImg" alt="QR code" width="280" height="280" class="w-[280px] h-[280px]"/>
                </div>
                <p class="font-mono type-body-sm text-slate mt-4 break-all" x-text="qrUrl"></p>
                <div class="flex items-center justify-center gap-2 mt-5">
                    <a :href="qrPng" class="btn btn-primary !py-2"><x-heroicon-o-arrow-down-tray class="w-4 h-4"/> PNG</a>
                    <a :href="qrSvg" class="btn btn-ghost !py-2"><x-heroicon-o-arrow-down-tray class="w-4 h-4"/> SVG</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
