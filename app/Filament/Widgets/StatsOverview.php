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
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count()),
            Stat::make('Short links', ShortLink::count()),
            Stat::make('Clicks today', Click::whereDate('created_at', today())->count()),
            Stat::make('Valid views today', Click::whereDate('created_at', today())->where('is_valid',true)->count()),
            Stat::make('Pending payouts', PayoutRequest::where('status','pending')->count())
                ->color('warning'),
            Stat::make('Paid this month', number_format(PayoutRequest::where('status','paid')
                ->whereMonth('processed_at', now()->month)->sum('amount')).' đ'),
        ];
    }
}
