<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Notifications\Notification;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importClientDetails')
                ->label('Import Client Details CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('Upload CSV File')
                        ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                        ->required()
                        ->helperText('CSV format: company_name, client_name, meeting_date, proposal, link_mockup, status, notes'),
                ])
                ->action(function (array $data) {
                    $file = storage_path('app/public/' . $data['csv_file']);

                    if (!file_exists($file)) {
                        Notification::make()
                            ->title('File not found')
                            ->danger()
                            ->send();
                        return;
                    }

                    $csv = array_map('str_getcsv', file($file));
                    $headers = array_shift($csv); // Remove header row

                    $clientDetails = [];
                    foreach ($csv as $row) {
                        if (count($row) >= 7) {
                            $clientDetails[] = [
                                'company_name' => $row[0] ?? '',
                                'client_name' => $row[1] ?? '',
                                'meeting_date' => $row[2] ?? null,
                                'proposal' => $row[3] ?? 'No',
                                'link_mockup' => $row[4] ?? '',
                                'status' => $row[5] ?? 'Progress',
                                'notes' => $row[6] ?? '',
                            ];
                        }
                    }

                    $this->record->client_details = $clientDetails;
                    $this->record->save();

                    Notification::make()
                        ->title('Import successful')
                        ->success()
                        ->body(count($clientDetails) . ' client details imported.')
                        ->send();

                    // Refresh form data
                    $this->fillForm();
                }),

            Actions\Action::make('exportClientDetails')
                ->label('Export Client Details CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->action(function () {
                    $clientDetails = $this->record->client_details ?? [];

                    if (empty($clientDetails)) {
                        Notification::make()
                            ->title('No data to export')
                            ->warning()
                            ->send();
                        return;
                    }

                    $filename = 'client-details-' . $this->record->judul . '-' . date('Y-m-d') . '.csv';
                    $filepath = storage_path('app/public/' . $filename);

                    $fp = fopen($filepath, 'w');

                    // Header
                    fputcsv($fp, ['company_name', 'client_name', 'meeting_date', 'proposal', 'link_mockup', 'status', 'notes']);

                    // Data
                    foreach ($clientDetails as $detail) {
                        fputcsv($fp, [
                            $detail['company_name'] ?? '',
                            $detail['client_name'] ?? '',
                            $detail['meeting_date'] ?? '',
                            $detail['proposal'] ?? 'No',
                            $detail['link_mockup'] ?? '',
                            $detail['status'] ?? 'Progress',
                            $detail['notes'] ?? '',
                        ]);
                    }

                    fclose($fp);

                    return response()->download($filepath)->deleteFileAfterSend(true);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
