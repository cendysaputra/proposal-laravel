<?php

namespace App\Filament\Widgets;

use App\Models\Proposal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ProposalsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Proposals per Bulan';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $now = now('Asia/Jakarta');
        $months = collect(range(11, 0))->map(function ($monthsAgo) use ($now) {
            return $now->copy()->subMonths($monthsAgo);
        });

        $proposalData = $months->map(function ($date) {
            return Proposal::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Proposals',
                    'data' => $proposalData->toArray(),
                    'backgroundColor' => 'rgba(0, 66, 88, 0.1)',
                    'borderColor' => 'rgba(0, 66, 88, 1)',
                    'fill' => true,
                ],
            ],
            'labels' => $months->map(fn ($date) => $date->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
