<x-app-layout :title="'Thống kê /'.$link->slug">
    <x-slot name="header">Thống kê liên kết</x-slot>

    @php
        $shortUrl = url('/'.$link->slug);
        $dStatus = $link->displayStatus();
        $statusBadge = ['active'=>'badge-success','disabled'=>'badge-neutral','blocked'=>'badge-critical','expired'=>'badge-warning','limit_reached'=>'badge-warning'][$dStatus] ?? 'badge-neutral';
        $statusLabel = ['active'=>'Hoạt động','disabled'=>'Đã tắt','blocked'=>'Bị chặn','expired'=>'Hết hạn','limit_reached'=>'Đạt giới hạn'][$dStatus] ?? $dStatus;
    @endphp

    <div class="max-w-[1400px] mx-auto space-y-8" x-data="{ qr: false }">

        {{-- Header --}}
        <div>
            <a href="{{ route('links.index') }}" class="inline-flex items-center gap-1 type-body-sm text-slate hover:text-ink-deep mb-4">
                <x-heroicon-m-arrow-left class="w-4 h-4"/> Quay lại danh sách
            </a>
            <div class="flex items-end justify-between flex-wrap gap-4">
                <div class="min-w-0">
                    <div class="section-label mb-3"><span>Phân tích chi tiết</span></div>
                    <h1 class="type-display-lg text-ink-deep font-mono break-all">/{{ $link->slug }}</h1>
                    <p class="type-body-md text-slate mt-2 truncate max-w-[640px]">→ {{ $link->original_url }}</p>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                        @if($link->expires_at)
                            <span class="type-caption text-stone flex items-center gap-1">
                                <x-heroicon-o-clock class="w-3.5 h-3.5"/> Hết hạn {{ $link->expires_at->format('d/m/Y H:i') }}
                            </span>
                        @endif
                        @if($link->max_clicks)
                            <span class="type-caption text-stone flex items-center gap-1">
                                <x-heroicon-o-flag class="w-3.5 h-3.5"/> {{ number_format($link->total_clicks) }}/{{ number_format($link->max_clicks) }} click
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="qr = true" class="btn btn-ghost">
                        <x-heroicon-o-qr-code class="w-4 h-4"/> QR code
                    </button>
                    <button type="button"
                            x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText('{{ $shortUrl }}'); copied = true; setTimeout(() => copied = false, 1500)"
                            class="btn btn-ghost">
                        <x-heroicon-o-clipboard class="w-4 h-4"/>
                        <span x-text="copied ? 'Đã copy ✓' : 'Copy link'"></span>
                    </button>
                    <a href="{{ route('links.edit', $link) }}" class="btn btn-primary">
                        <x-heroicon-o-pencil-square class="w-4 h-4"/> Sửa
                    </a>
                </div>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-icon-feature !p-5 relative overflow-hidden">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary-soft text-primary-deep">
                    <x-heroicon-o-cursor-arrow-rays class="w-5 h-5"/>
                </div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Tổng click</div>
                <div class="type-heading-lg text-ink-deep mt-1">{{ number_format($link->total_clicks) }}</div>
            </div>
            <div class="card-icon-feature !p-5">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-[color:var(--color-success-soft)] text-success">
                    <x-heroicon-o-check-circle class="w-5 h-5"/>
                </div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">View hợp lệ</div>
                <div class="type-heading-lg text-ink-deep mt-1">{{ number_format($link->valid_views) }}</div>
            </div>
            <div class="card-icon-feature !p-5">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-[color:var(--color-warning)]/20 text-[color:var(--color-attention)]">
                    <x-heroicon-o-shield-check class="w-5 h-5"/>
                </div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Tỉ lệ hợp lệ</div>
                <div class="type-heading-lg text-ink-deep mt-1">{{ $validRate }}%</div>
            </div>
            <div class="card-icon-feature !p-5 !border-primary !bg-primary-soft">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-canvas text-primary-deep">
                    <x-heroicon-s-banknotes class="w-5 h-5"/>
                </div>
                <div class="type-caption-bold uppercase tracking-wider text-primary-deep mt-4">Doanh thu</div>
                <div class="type-heading-lg text-primary-deep mt-1">{{ number_format($link->total_earned) }}<span class="type-subtitle-md ml-1">đ</span></div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card-feature !p-8">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                <div>
                    <div class="section-label mb-2"><span>Lượt truy cập {{ $days }} ngày</span></div>
                    <h2 class="type-heading-sm text-ink-deep">Click & View hợp lệ theo ngày</h2>
                </div>
                <div class="flex items-center gap-2">
                    @foreach([7 => '7 ngày', 30 => '30 ngày', 90 => '90 ngày'] as $d => $label)
                        <a href="{{ route('links.stats', ['link' => $link, 'days' => $d]) }}"
                           class="pill-tab !py-1 !px-3 type-caption-bold {{ $days === $d ? 'active' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="h-72"><canvas id="timeline"></canvas></div>
        </div>

        {{-- Breakdown row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Device --}}
            <div class="card-feature !p-6">
                <div class="section-label mb-1"><span>Thiết bị</span></div>
                <h3 class="type-heading-sm mb-4">Phân bố device</h3>
                <x-stat-bars :items="$devices" empty="Chưa có dữ liệu."/>
            </div>

            {{-- Browser bars --}}
            <div class="card-feature !p-6">
                <div class="section-label mb-1"><span>Trình duyệt</span></div>
                <h3 class="type-heading-sm mb-4">Top browser</h3>
                <x-stat-bars :items="$browsers" empty="Chưa có dữ liệu."/>
            </div>

            {{-- OS bars --}}
            <div class="card-feature !p-6">
                <div class="section-label mb-1"><span>Hệ điều hành</span></div>
                <h3 class="type-heading-sm mb-4">Top OS</h3>
                <x-stat-bars :items="$oses" empty="Chưa có dữ liệu."/>
            </div>
        </div>

        {{-- Top referrers --}}
        <div class="card-feature !p-6">
            <div class="section-label mb-1"><span>Nguồn truy cập</span></div>
            <h3 class="type-heading-sm mb-4">Top nguồn dẫn (referrer)</h3>
            <x-stat-bars :items="$topReferers" empty="Chưa có lượt truy cập nào." mono/>
        </div>

        {{-- QR modal --}}
        <div x-show="qr" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-deep/60 backdrop-blur-sm"
             x-on:click="qr = false" x-on:keydown.escape.window="qr = false">
            <div class="bg-canvas rounded-3xl border border-hairline-soft shadow-xl max-w-[400px] w-full p-8 text-center"
                 x-on:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="type-heading-sm text-ink-deep">QR code</h3>
                    <button type="button" x-on:click="qr = false" class="btn-icon-ghost"><x-heroicon-m-x-mark class="w-5 h-5"/></button>
                </div>
                <div class="rounded-2xl border border-hairline-soft p-4 bg-white inline-block">
                    <img src="{{ route('links.qr', $link) }}" alt="QR code cho /{{ $link->slug }}" width="280" height="280" class="w-[280px] h-[280px]"/>
                </div>
                <p class="font-mono type-body-sm text-slate mt-4 break-all">{{ $shortUrl }}</p>
                <div class="flex items-center justify-center gap-2 mt-5">
                    <a href="{{ route('links.qr', ['link' => $link, 'download' => 1]) }}" class="btn btn-primary !py-2">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4"/> PNG
                    </a>
                    <a href="{{ route('links.qr', ['link' => $link, 'format' => 'svg', 'download' => 1]) }}" class="btn btn-ghost !py-2">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4"/> SVG
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const font = { family: 'Public Sans', size: 11, weight: '700' };

            new Chart(document.getElementById('timeline'), {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        { label: 'Click', data: @json($totals), borderColor: '#0064E0', backgroundColor: 'rgba(0,100,224,0.08)', tension: 0.3, borderWidth: 2.5, fill: true, pointRadius: 0, pointHoverRadius: 5 },
                        { label: 'View hợp lệ', data: @json($valids), borderColor: '#2E7D32', backgroundColor: 'transparent', tension: 0.3, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5 },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top', align: 'end', labels: { boxWidth: 12, padding: 16, font } }, tooltip: { backgroundColor: '#0A1317', padding: 12 } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(10,19,23,0.06)' }, ticks: { font } }, x: { grid: { display: false }, ticks: { font } } }
                }
            });
        });
    </script>
</x-app-layout>
