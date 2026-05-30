<?php
namespace App\Filament\Widgets;

use App\Models\AdCampaign;
use Filament\Widgets\ChartWidget;

class AdPerformanceChart extends ChartWidget
{
    protected static ?string $heading = '📣 CTR theo chiến dịch quảng cáo (Top 8)';
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $campaigns = AdCampaign::query()
            ->where('impressions', '>', 0)
            ->get(['name', 'impressions', 'clicks_count'])
            ->map(fn ($c) => [
                'name' => $c->name,
                'ctr' => round($c->clicks_count / max($c->impressions, 1) * 100, 2),
            ])
            ->sortByDesc('ctr')
            ->take(8)
            ->values();

        return [
            'datasets' => [[
                'label' => 'CTR (%)',
                'data' => $campaigns->pluck('ctr')->toArray(),
                'backgroundColor' => [
                    '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4',
                    '#ec4899', '#22c55e', '#3b82f6', '#f97316',
                ],
                'borderRadius' => 6,
            ]],
            'labels' => $campaigns->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['beginAtZero' => true, 'title' => ['display' => true, 'text' => 'CTR %']]],
        ];
    }
}
