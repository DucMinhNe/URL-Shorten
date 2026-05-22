<?php
namespace App\Filament\Widgets;

use App\Models\Click;
use Filament\Widgets\ChartWidget;

class ClicksChart extends ChartWidget
{
    protected static ?string $heading = 'Clicks (last 30 days)';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn($d)=>now()->subDays($d)->format('Y-m-d'));
        $totals = $days->map(fn($d)=>Click::whereDate('created_at',$d)->count());
        $valid = $days->map(fn($d)=>Click::whereDate('created_at',$d)->where('is_valid',true)->count());

        return [
            'datasets' => [
                ['label'=>'Total','data'=>$totals->toArray(),'borderColor'=>'#3b82f6'],
                ['label'=>'Valid','data'=>$valid->toArray(),'borderColor'=>'#10b981'],
            ],
            'labels' => $days->map(fn($d)=>substr($d,5))->toArray(),
        ];
    }

    protected function getType(): string { return 'line'; }
}
