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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Enforce published_at logic based on form state
        // If published_at is null in the form data, ensure it stays null
        if (array_key_exists('published_at', $data) && $data['published_at'] === null) {
            $data['published_at'] = null;
        }

        return $data;
    }
}
