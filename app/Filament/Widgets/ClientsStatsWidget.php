<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalClients = Client::count();

        // Calculate total clients from all client_details
        $allClients = Client::all();
        $totalClientDetails = 0;
        $dealCount = 0;
        $progressCount = 0;
        $cancelCount = 0;

        foreach ($allClients as $client) {
            $details = is_array($client->client_details) ? $client->client_details : [];
            $totalClientDetails += count($details);
            $dealCount += collect($details)->where('status', 'Deal')->count();
            $progressCount += collect($details)->where('status', 'Progress')->count();
            $cancelCount += collect($details)->where('status', 'Cancel')->count();
        }

        return [
            Stat::make('Total Data Clients', $totalClients)
                ->description('Total data klien yang tersimpan')
                ->descriptionIcon('heroicon-m-folder')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url(route('clients.index'), shouldOpenInNewTab: true),

            Stat::make('Total Klien Masuk', $totalClientDetails)
                ->description('Total semua klien yang masuk')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Deal', $dealCount)
                ->description($totalClientDetails > 0 ? round(($dealCount / $totalClientDetails) * 100, 1) . '% dari total klien' : '0%')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Progress', $progressCount)
                ->description($totalClientDetails > 0 ? round(($progressCount / $totalClientDetails) * 100, 1) . '% dari total klien' : '0%')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
