<?php
namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Người dùng mới (30 ngày qua)';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $rows = User::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $days = collect(range(29, 0))->map(fn ($n) => now()->subDays($n)->format('Y-m-d'));
        $data = $days->map(fn ($d) => (int) ($rows[$d] ?? 0));

        return [
            'datasets' => [[
                'label' => 'Người dùng mới',
                'data' => $data->toArray(),
                'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
                'borderColor' => '#6366f1',
                'borderRadius' => 4,
            ]],
            'labels' => $days->map(fn ($d) => substr($d, 5))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
