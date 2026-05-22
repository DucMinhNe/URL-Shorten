<x-app-layout :title="'Liên kết của tôi'">
    <x-slot name="header">Liên kết của tôi</x-slot>

    <div class="max-w-[1400px] mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-end justify-between flex-wrap gap-4">
            <div>
                <div class="section-label mb-3"><span>Quản lý liên kết</span></div>
                <h1 class="type-display-lg text-ink-deep">Tất cả liên kết<br><span class="font-light italic text-slate">của bạn.</span></h1>
                <p class="type-body-md text-slate mt-3">
                    {{ $links->total() }} liên kết · {{ number_format(auth()->user()->shortLinks()->sum('total_clicks')) }} click · {{ number_format(auth()->user()->shortLinks()->sum('total_earned')) }}đ doanh thu
                </p>
            </div>
            <a href="{{ route('links.create') }}" class="btn btn-primary">
                <x-heroicon-m-plus class="w-4 h-4"/>
                Tạo liên kết mới
            </a>
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
        <div class="card-feature !p-3 !rounded-2xl flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 search-pill !flex-1 !min-w-[200px] max-w-[300px]">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-steel ml-2"/>
                <input type="text" placeholder="Tìm theo slug hoặc URL..." class="bg-transparent border-0 outline-none flex-1 type-body-sm"/>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button class="pill-tab active !py-1.5 !px-3 type-caption-bold">Tất cả</button>
                <button class="pill-tab !py-1.5 !px-3 type-caption-bold">Hoạt động</button>
                <button class="pill-tab !py-1.5 !px-3 type-caption-bold">Đã tắt</button>
            </div>
            <button class="pill-tab !py-1.5 !px-3 type-caption-bold ml-auto">
                <x-heroicon-o-arrows-up-down class="w-3.5 h-3.5"/>
                Mới nhất
            </button>
        </div>

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
                            <th class="text-left px-6 py-3">Liên kết ngắn</th>
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
                                $statusBadge = ['active'=>'badge-success','disabled'=>'badge-neutral','blocked'=>'badge-critical'][$link->status] ?? 'badge-neutral';
                                $statusLabel = ['active'=>'Hoạt động','disabled'=>'Đã tắt','blocked'=>'Bị chặn'][$link->status] ?? $link->status;
                                $validRate = $link->total_clicks > 0 ? round($link->valid_views / $link->total_clicks * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-surface-soft transition-colors group">
                                <td class="px-6 py-4">
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
                                            <div class="type-caption text-stone">{{ $link->created_at->diffForHumans() }}</div>
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
                                        <button onclick="navigator.clipboard.writeText('{{ $shortUrl }}'); this.classList.add('text-success')" class="btn-icon-ghost" title="Copy short URL">
                                            <x-heroicon-o-clipboard class="w-4 h-4"/>
                                        </button>
                                        <a href="{{ route('links.edit', $link) }}" class="btn-icon-ghost" title="Sửa">
                                            <x-heroicon-o-pencil-square class="w-4 h-4"/>
                                        </a>
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
    </div>
</x-app-layout>
