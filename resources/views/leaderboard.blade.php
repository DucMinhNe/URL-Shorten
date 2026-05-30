<x-guest-layout :title="'Bảng xếp hạng · LinkPay'"
    description="Top thành viên kiếm nhiều nhất trên LinkPay. Rút gọn link, chia sẻ và kiếm tiền theo mỗi view hợp lệ.">
<x-public-nav active="leaderboard"/>

<section class="relative overflow-hidden" style="background:linear-gradient(180deg,#0B0B14 0%,#15172A 100%);">
    <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-40 blur-3xl" style="background:radial-gradient(circle,#696CFF 0%,transparent 70%);"></div>
    <div class="absolute -bottom-32 -right-24 w-96 h-96 rounded-full opacity-30 blur-3xl" style="background:radial-gradient(circle,#EC4899 0%,transparent 70%);"></div>

    <div class="max-w-[1000px] mx-auto px-6 py-16 relative">
        <div class="text-center">
            <span class="lp-tag lp-tag-amber">🏆 Cập nhật realtime</span>
            <h1 class="text-white font-black mt-4" style="font-size:clamp(34px,5vw,56px);line-height:1.05;">
                Bảng xếp hạng <span class="lp-grad-text">thu nhập</span>
            </h1>
            <p class="text-white/60 mt-3 max-w-[560px] mx-auto">Những thành viên kiếm nhiều nhất từ việc rút gọn & chia sẻ link. Tên được ẩn một phần để bảo vệ riêng tư.</p>
            <div class="flex items-center justify-center gap-8 mt-7">
                <div>
                    <div class="text-white text-3xl font-black"><span data-count="{{ $totalPaid }}" data-suffix="đ">0</span></div>
                    <div class="text-white/50 text-xs uppercase tracking-wider mt-1">Tổng đã kiếm</div>
                </div>
                <div class="w-px h-12 bg-white/15"></div>
                <div>
                    <div class="text-white text-3xl font-black"><span data-count="{{ $members }}">0</span></div>
                    <div class="text-white/50 text-xs uppercase tracking-wider mt-1">Thành viên</div>
                </div>
            </div>
        </div>

        {{-- Podium top 3 --}}
        @php $podium = $top->take(3); @endphp
        @if($podium->count() === 3)
            <div class="grid grid-cols-3 gap-3 sm:gap-5 mt-12 items-end max-w-[680px] mx-auto">
                @foreach([1 => 'h-32', 0 => 'h-40', 2 => 'h-24'] as $idx => $h)
                    @php $p = $podium[$idx]; $medal = ['🥇','🥈','🥉'][$p->rank-1]; @endphp
                    <div class="text-center">
                        <div class="text-4xl mb-2">{{ $medal }}</div>
                        <div class="text-white font-bold truncate px-1">{{ $p->name }}</div>
                        <div class="text-white/50 text-xs mb-2">{{ number_format($p->earned) }}đ</div>
                        <div class="{{ $h }} rounded-t-2xl"
                             style="background:linear-gradient(180deg,{{ ['#FBBF24','#CBD5E1','#D97706'][$p->rank-1] }} 0%,rgba(255,255,255,.05) 100%);
                                    box-shadow:0 -10px 40px -10px {{ ['rgba(251,191,36,.6)','rgba(203,213,225,.5)','rgba(217,119,6,.5)'][$p->rank-1] }};">
                            <div class="pt-3 text-white/90 font-black text-2xl">#{{ $p->rank }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="bg-app-soft py-14">
    <div class="max-w-[760px] mx-auto px-6">
        <div class="bg-white rounded-3xl border border-hairline-soft overflow-hidden shadow-sm divide-y divide-hairline-soft">
            @forelse($top as $row)
                <div class="px-5 sm:px-6 py-4 flex items-center gap-4" @if($row->rank <= 3) style="background:rgba(245,158,11,.06);" @endif>
                    <div class="lp-rank lp-rank-{{ $row->rank <= 3 ? $row->rank : '' }}">{{ $row->rank }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-ink-deep truncate">{{ $row->name }}</div>
                        <div class="text-xs text-stone">Tham gia {{ $row->since->translatedFormat('m/Y') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-extrabold lp-grad-text-green">{{ number_format($row->earned) }}đ</div>
                        <div class="text-xs text-stone">đã kiếm</div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-stone">Chưa có dữ liệu xếp hạng.</div>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <p class="text-slate">Muốn có tên trong bảng này?</p>
            <a href="{{ route('register') }}" class="lp-btn-grad mt-4">Bắt đầu kiếm tiền <x-heroicon-m-arrow-right class="w-4 h-4"/></a>
        </div>
    </div>
</section>

<x-public-footer/>
</x-guest-layout>
