<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ClientsStatsWidget;
use App\Filament\Widgets\InvoicesChartWidget;
use App\Filament\Widgets\ProposalsChartWidget;
use App\Filament\Widgets\ProposalsStatsWidget;
use App\Filament\Widgets\RecentClientsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string
    {
        $hour = now('Asia/Jakarta')->format('H');
        $greeting = match(true) {
            $hour >= 5 && $hour < 11 => 'Selamat pagi',
            $hour >= 11 && $hour < 15 => 'Selamat siang',
            $hour >= 15 && $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $userName = auth()->user()->name ?? 'User';

        return "{$greeting}, {$userName}";
    }

    public function getWidgets(): array
    {
        return [
            ProposalsStatsWidget::class,
            ClientsStatsWidget::class,
            ProposalsChartWidget::class,
            InvoicesChartWidget::class,
            RecentClientsWidget::class,
            AccountWidget::class,
        ];
    }
}
