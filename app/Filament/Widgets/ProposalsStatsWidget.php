<?php

namespace App\Filament\Widgets;

use App\Models\Proposal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProposalsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProposals = Proposal::count();
        $draftProposals = Proposal::whereNull('published_at')->count();
        $publishedProposals = Proposal::whereNotNull('published_at')->count();
        $lockedProposals = Proposal::where('is_locked', true)->count();

        return [
            Stat::make('Total Proposals', $totalProposals)
                ->description('All proposals')
                ->color('primary'),

            Stat::make('Draft', $draftProposals)
                ->description('Unpublished proposals')
                ->color('gray'),

            Stat::make('Published', $publishedProposals)
                ->description('Published proposals')
                ->color('success'),

            Stat::make('Locked', $lockedProposals)
                ->description('Locked proposals')
                ->color('warning'),
        ];
    }
}
