<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label('View Invoice')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('invoices.show', $record->slug))
                ->openUrlInNewTab()
                ->color('info'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
