<?php
namespace App\Filament\Widgets;

use App\Models\Click;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Doanh thu click (30 ngày qua)';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $rows = Click::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as d, SUM(earnings) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $days = collect(range(29, 0))->map(fn ($n) => now()->subDays($n)->format('Y-m-d'));
        $data = $days->map(fn ($d) => (int) ($rows[$d] ?? 0));

        return [
            'datasets' => [[
                'label' => 'Doanh thu (đ)',
                'data' => $data->toArray(),
                'borderColor' => '#16a34a',
                'backgroundColor' => 'rgba(22, 163, 74, 0.15)',
                'fill' => true,
                'tension' => 0.35,
                'pointBackgroundColor' => '#16a34a',
            ]],
            'labels' => $days->map(fn ($d) => substr($d, 5))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
