<?php
namespace App\Filament\Widgets;

use App\Models\Click;
use App\Models\PayoutRequest;
use App\Models\ShortLink;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 3;
    }

    /** Daily counts for the last 7 days for a sparkline. */
    protected function spark(\Closure $perDay): array
    {
        return collect(range(6, 0))
            ->map(fn ($n) => (int) $perDay(now()->subDays($n)))
            ->toArray();
    }

    protected function getStats(): array
    {
        // Users
        $totalUsers = User::count();
        $usersSpark = $this->spark(fn ($d) => User::whereDate('created_at', $d)->count());
        $newUsersToday = $usersSpark[6];

        // Active links
        $activeLinks = ShortLink::where('status', 'active')->count();
        $totalLinks = ShortLink::count();

        // Clicks today
        $clicksToday = Click::whereDate('created_at', today())->count();
        $clicksYesterday = Click::whereDate('created_at', today()->subDay())->count();
        $clicksSpark = $this->spark(fn ($d) => Click::whereDate('created_at', $d)->count());
        $clicksTrendUp = $clicksToday >= $clicksYesterday;

        // Revenue today
        $revenueToday = (int) Click::whereDate('created_at', today())->sum('earnings');
        $revenueYesterday = (int) Click::whereDate('created_at', today()->subDay())->sum('earnings');
        $revenueSpark = $this->spark(fn ($d) => (int) Click::whereDate('created_at', $d)->sum('earnings'));
        $revenueTrendUp = $revenueToday >= $revenueYesterday;

        // Pending payouts
        $pendingCount = PayoutRequest::where('status', 'pending')->count();
        $pendingAmount = (int) PayoutRequest::where('status', 'pending')->sum('amount');

        // Paid this month
        $paidThisMonth = (int) PayoutRequest::where('status', 'paid')
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->sum('amount');
        $paidCount = PayoutRequest::where('status', 'paid')
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->count();

        return [
            Stat::make('Tổng người dùng', number_format($totalUsers))
                ->description("+{$newUsersToday} hôm nay")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($usersSpark)
                ->color('primary'),

            Stat::make('Liên kết đang hoạt động', number_format($activeLinks))
                ->description(number_format($totalLinks) . ' tổng liên kết')
                ->descriptionIcon('heroicon-m-link')
                ->color('info'),

            Stat::make('Click hôm nay', number_format($clicksToday))
                ->description(($clicksTrendUp ? '+' : '') . number_format($clicksToday - $clicksYesterday) . ' so với hôm qua')
                ->descriptionIcon($clicksTrendUp ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($clicksSpark)
                ->color($clicksTrendUp ? 'success' : 'danger'),

            Stat::make('Doanh thu hôm nay', number_format($revenueToday) . ' đ')
                ->description(($revenueTrendUp ? '+' : '') . number_format($revenueToday - $revenueYesterday) . ' đ so với hôm qua')
                ->descriptionIcon($revenueTrendUp ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($revenueSpark)
                ->color('success'),

            Stat::make('Yêu cầu rút tiền chờ', number_format($pendingCount))
                ->description(number_format($pendingAmount) . ' đ đang chờ duyệt')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Đã chi trả tháng này', number_format($paidThisMonth) . ' đ')
                ->description("{$paidCount} lượt rút thành công")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
