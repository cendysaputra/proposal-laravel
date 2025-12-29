<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProposal extends EditRecord
{
    protected static string $resource = ProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label('View Proposal')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('proposals.show', $record->slug))
                ->openUrlInNewTab()
                ->color('info'),
            Actions\DeleteAction::make(),
        ];
    }
}
