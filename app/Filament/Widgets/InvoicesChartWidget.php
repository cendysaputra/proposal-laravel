<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class InvoicesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Invoices per Bulan';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $now = now('Asia/Jakarta');
        $months = collect(range(11, 0))->map(function ($monthsAgo) use ($now) {
            return $now->copy()->subMonths($monthsAgo);
        });

        $invoiceData = $months->map(function ($date) {
            return Invoice::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Invoices',
                    'data' => $invoiceData->toArray(),
                    'backgroundColor' => 'rgba(255, 159, 64, 0.1)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
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
