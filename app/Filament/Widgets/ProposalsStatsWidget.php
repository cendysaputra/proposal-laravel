<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Proposal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProposalsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $publishedProposals = Proposal::whereNotNull('published_at')->count();
        $draftProposals = Proposal::whereNull('published_at')->count();
        $publishedInvoices = Invoice::whereNotNull('published_at')->count();
        $draftInvoices = Invoice::whereNull('published_at')->count();

        return [
            Stat::make('Proposal Published', $publishedProposals),
            Stat::make('Proposal Draft', $draftProposals),
            Stat::make('Invoice Published', $publishedInvoices),
            Stat::make('Invoice Draft', $draftInvoices),
        ];
    }
}
