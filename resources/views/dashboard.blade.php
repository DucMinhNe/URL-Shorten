<x-app-layout :title="'Tổng quan'">
    <x-slot name="header">Tổng quan</x-slot>

    <div class="max-w-[1400px] mx-auto space-y-8">

        {{-- Welcome banner --}}
        <div>
            <div class="section-label mb-3"><span>Tổng quan · {{ now()->format('d/m/Y') }}</span></div>
            <h1 class="type-display-lg text-ink-deep">
                Chào, {{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name)) - 1] ?? 'bạn' }}
                <span class="font-light italic text-slate">👋</span>
            </h1>
            <p class="type-subtitle-md text-charcoal mt-3 max-w-[640px]">
                Đây là tổng quan thu nhập 30 ngày qua. Tiếp tục chia sẻ link để tăng doanh thu.
            </p>
        </div>

        {{-- KPI row --}}
        @php $validRate = $stats['total_clicks'] > 0 ? round($stats['valid_views'] / $stats['total_clicks'] * 100, 1) : 0; @endphp
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

            <div class="card-icon-feature lp-kpi lp-accent-cyan lp-rise d1 !p-5">
                <div class="lp-ic lp-ic-cyan"><x-heroicon-o-link class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Liên kết</div>
                <div class="type-heading-lg text-ink-deep mt-1" data-count="{{ $stats['total_links'] }}">0</div>
                <div class="type-caption text-slate mt-1">đang quản lý</div>
            </div>

            <div class="card-icon-feature lp-kpi lp-accent-warm lp-rise d2 !p-5">
                <div class="lp-ic lp-ic-amber"><x-heroicon-o-cursor-arrow-rays class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Tổng click</div>
                <div class="type-heading-lg text-ink-deep mt-1" data-count="{{ $stats['total_clicks'] }}">0</div>
                <div class="mt-2"><x-spark :points="$sparkClicks" color="#F59E0B"/></div>
            </div>

            <div class="card-icon-feature lp-kpi lp-accent-green lp-rise d3 !p-5">
                <div class="lp-ic lp-ic-green"><x-heroicon-o-check-circle class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">View hợp lệ</div>
                <div class="type-heading-lg text-ink-deep mt-1" data-count="{{ $stats['valid_views'] }}">0</div>
                <div class="mt-2"><x-spark :points="$sparkValids" color="#10B981"/></div>
            </div>

            <div class="card-icon-feature lp-kpi lp-accent-pink lp-rise d4 !p-5">
                <div class="lp-ic lp-ic-pink"><x-heroicon-o-calendar-days class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Tháng này</div>
                <div class="type-heading-lg text-ink-deep mt-1"><span data-count="{{ $earnedThisMonth }}">0</span><span class="type-subtitle-md ml-1">đ</span></div>
                <div class="type-caption mt-1">
                    @if($monthDelta === null)
                        <span class="text-slate">tháng đầu tiên</span>
                    @else
                        <span class="lp-delta {{ $monthDelta >= 0 ? 'lp-delta-up' : 'lp-delta-down' }}">
                            {{ $monthDelta >= 0 ? '▲' : '▼' }} {{ abs($monthDelta) }}%
                        </span>
                        <span class="text-slate">vs tháng trước</span>
                    @endif
                </div>
            </div>

            <div class="card-icon-feature lp-kpi-hero lp-rise d5 !p-5">
                <div class="lp-ic !bg-white/20"><x-heroicon-s-banknotes class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-white/70 mt-4">Số dư khả dụng</div>
                <div class="type-heading-lg mt-1"><span data-count="{{ $stats['balance'] }}">0</span><span class="type-subtitle-md ml-1">đ</span></div>
                <a href="{{ route('payout.index') }}" class="type-caption-bold text-white mt-1 inline-flex items-center gap-1">
                    Rút ngay <x-heroicon-m-arrow-right class="w-3 h-3"/>
                </a>
            </div>

            <div class="card-icon-feature lp-kpi lp-rise d6 !p-5">
                <div class="lp-ic lp-ic-violet"><x-heroicon-o-trophy class="w-5 h-5"/></div>
                <div class="type-caption-bold uppercase tracking-wider text-stone mt-4">Tổng tích lũy</div>
                <div class="type-heading-lg text-ink-deep mt-1"><span data-count="{{ $stats['total_earned'] }}">0</span><span class="type-subtitle-md ml-1">đ</span></div>
                <div class="type-caption text-slate mt-1">đã kiếm · {{ $validRate }}% hợp lệ</div>
            </div>
        </div>

        {{-- Chart card --}}
        <div class="card-feature !p-8">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                <div>
                    <div class="section-label mb-2"><span>Hiệu suất 30 ngày</span></div>
                    <h2 class="type-heading-sm text-ink-deep">Click & Doanh thu theo ngày</h2>
                </div>
                <div class="flex items-center gap-2">
                    @foreach([7 => '7 ngày', 30 => '30 ngày', 90 => '90 ngày'] as $d => $label)
                        <a href="{{ route('dashboard', ['days' => $d]) }}"
                           class="pill-tab !py-1 !px-3 type-caption-bold {{ $days === $d ? 'active' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="h-72">
                <canvas id="chart"></canvas>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-hairline-soft">
                <div>
                    <div class="type-caption-bold uppercase tracking-wider text-stone">TB / ngày</div>
                    <div class="type-heading-sm text-ink-deep mt-1">{{ number_format(array_sum($totals) / max(1, $days)) }}</div>
                    <div class="type-caption text-slate">click trong {{ $days }} ngày</div>
                </div>
                <div>
                    <div class="type-caption-bold uppercase tracking-wider text-stone">TB doanh thu</div>
                    <div class="type-heading-sm text-ink-deep mt-1">{{ number_format(array_sum($earnings) / max(1, $days)) }}đ</div>
                    <div class="type-caption text-slate">mỗi ngày</div>
                </div>
                <div>
                    <div class="type-caption-bold uppercase tracking-wider text-stone">Tốc độ</div>
                    @if($growthRate === null)
                        <div class="type-heading-sm text-stone mt-1">—</div>
                        <div class="type-caption text-slate">chưa đủ dữ liệu</div>
                    @else
                        <div class="type-heading-sm {{ $growthRate >= 0 ? 'text-success' : 'text-critical' }} mt-1">
                            {{ $growthRate >= 0 ? '+' : '' }}{{ $growthRate }}%
                        </div>
                        <div class="type-caption text-slate">so với {{ $days }} ngày trước</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Two-col row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Top links --}}
            <div class="lg:col-span-2 card-feature !p-0 overflow-hidden">
                <div class="p-6 flex items-center justify-between border-b border-hairline-soft">
                    <div>
                        <div class="section-label mb-1"><span>Top 5 liên kết</span></div>
                        <h3 class="type-heading-sm">Đang chạy mạnh nhất</h3>
                    </div>
                    <a href="{{ route('links.index') }}" class="btn btn-ghost !py-2">
                        Xem tất cả
                        <x-heroicon-m-arrow-right class="w-4 h-4"/>
                    </a>
                </div>

                @php
                    $topLinks = auth()->user()->shortLinks()->orderByDesc('total_clicks')->limit(5)->get();
                @endphp

                @if($topLinks->isEmpty())
                    <div class="p-12 text-center">
                        <x-heroicon-o-link class="w-12 h-12 text-stone mx-auto"/>
                        <p class="type-body-md text-slate mt-3">Chưa có liên kết nào. Tạo cái đầu tiên thôi!</p>
                        <a href="{{ route('links.create') }}" class="btn btn-primary mt-4">
                            <x-heroicon-m-plus class="w-4 h-4"/>
                            Tạo liên kết
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-hairline-soft">
                        @foreach($topLinks as $link)
                            <div class="p-5 flex items-center gap-4 hover:bg-surface-soft transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-surface-soft flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-link class="w-5 h-5 text-charcoal"/>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-mono type-body-sm-bold text-ink-deep truncate">/{{ $link->slug }}</div>
                                    <div class="type-caption text-stone truncate">{{ $link->original_url }}</div>
                                </div>
                                <div class="hidden sm:block text-right flex-shrink-0">
                                    <div class="type-body-sm-bold text-ink-deep">{{ number_format($link->total_clicks) }}</div>
                                    <div class="type-caption text-stone">click</div>
                                </div>
                                <div class="text-right flex-shrink-0 min-w-[80px]">
                                    <div class="type-body-sm-bold text-success">{{ number_format($link->total_earned) }}đ</div>
                                    <div class="type-caption text-stone">đã kiếm</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick actions --}}
            <div class="space-y-6">
                <div class="card-promo-dark !p-8">
                    <div class="type-caption-bold uppercase tracking-wider text-stone">Sẵn sàng rút tiền?</div>
                    <div class="type-heading-md text-on-dark mt-2 font-light italic">{{ number_format($stats['balance']) }}đ trong ví</div>
                    <p class="type-body-sm text-stone mt-3">
                        @if($stats['balance'] >= 100000)
                            Đủ điều kiện rút. Admin duyệt trong 24h.
                        @else
                            Cần thêm {{ number_format(100000 - $stats['balance']) }}đ để có thể rút (min 100.000đ).
                        @endif
                    </p>
                    @if($stats['balance'] >= 100000)
                        <a href="{{ route('payout.index') }}" class="btn btn-buy mt-5">
                            Yêu cầu rút tiền
                            <x-heroicon-m-arrow-right class="w-4 h-4"/>
                        </a>
                    @else
                        <a href="{{ route('links.create') }}" class="btn btn-secondary !border-stone/40 !text-on-dark hover:!bg-white/10 mt-5">
                            Tạo thêm liên kết
                            <x-heroicon-m-plus class="w-4 h-4"/>
                        </a>
                    @endif
                </div>

                {{-- Recent activity feed --}}
                <div class="card-feature !p-0 overflow-hidden">
                    <div class="p-6 pb-4 border-b border-hairline-soft">
                        <div class="section-label mb-1"><span>Thời gian thực</span></div>
                        <h3 class="type-heading-sm">Hoạt động gần đây</h3>
                    </div>
                    @if($recentClicks->isEmpty())
                        <div class="p-8 text-center">
                            <x-heroicon-o-cursor-arrow-rays class="w-10 h-10 text-stone mx-auto"/>
                            <p class="type-body-sm text-slate mt-2">Chưa có lượt click nào. Chia sẻ link để bắt đầu!</p>
                        </div>
                    @else
                        <div class="divide-y divide-hairline-soft">
                            @foreach($recentClicks as $c)
                                @php
                                    $device = \App\Support\UserAgentParser::deviceType($c->user_agent);
                                    $source = \App\Support\UserAgentParser::refererSource($c->referer);
                                    $icon = $device === 'Mobile' ? 'heroicon-o-device-phone-mobile' : ($device === 'Tablet' ? 'heroicon-o-device-tablet' : 'heroicon-o-computer-desktop');
                                @endphp
                                <div class="px-6 py-3 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $c->is_valid ? 'bg-[color:var(--color-success-soft)] text-success' : 'bg-surface-soft text-stone' }}">
                                        <x-dynamic-component :component="$icon" class="w-4 h-4"/>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="type-body-sm-bold text-ink-deep truncate">
                                            <span class="font-mono">/{{ $c->slug }}</span>
                                        </div>
                                        <div class="type-caption text-stone truncate">{{ $device }} · {{ $source }} · {{ $c->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if($c->is_valid)
                                        <span class="type-caption-bold text-success flex-shrink-0">+{{ number_format($c->earnings) }}đ</span>
                                    @else
                                        <span class="badge badge-neutral flex-shrink-0 !py-0.5">Bỏ qua</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card-feature !p-6">
                    <div class="section-label mb-3"><span>Mẹo tăng thu nhập</span></div>
                    <h3 class="type-heading-sm">Chia sẻ vào nhóm lớn</h3>
                    <p class="type-body-sm text-slate mt-2">Đăng link vào Facebook Group hoặc Zalo cộng đồng. View hợp lệ tăng 3-5x.</p>
                </div>
            </div>
        </div>
    </div>

    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('chart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Click',
                            data: @json($totals),
                            borderColor: '#0064E0',
                            backgroundColor: 'rgba(0,100,224,0.08)',
                            tension: 0.3,
                            borderWidth: 2.5,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#0064E0',
                        },
                        {
                            label: 'Doanh thu (đ)',
                            data: @json($earnings),
                            borderColor: '#2E7D32',
                            backgroundColor: 'transparent',
                            tension: 0.3,
                            borderWidth: 2.5,
                            yAxisID: 'y1',
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#2E7D32',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 12, boxHeight: 12, padding: 16, font: { family: 'Montserrat', size: 12, weight: '700' } } },
                        tooltip: { backgroundColor: '#0A1317', titleFont: { family: 'Montserrat', weight: '700' }, bodyFont: { family: 'Montserrat' }, padding: 12, borderRadius: 8 }
                    },
                    scales: {
                        y:  { type: 'linear', position: 'left', beginAtZero: true, grid: { color: 'rgba(10,19,23,0.06)' }, ticks: { font: { family: 'Montserrat', size: 11 } } },
                        y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, ticks: { font: { family: 'Montserrat', size: 11 } } },
                        x:  { grid: { display: false }, ticks: { font: { family: 'Montserrat', size: 11 } } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
