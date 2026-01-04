<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Notifications\Notification;

class CreateClient extends CreateRecord
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
                        // Skip empty rows or rows with invalid data
                        if (count($row) < 7) {
                            continue;
                        }

                        $companyName = trim($row[0] ?? '');
                        $clientName = trim($row[1] ?? '');

                        // Skip if company_name or client_name is empty or contains TRUE/FALSE
                        if (empty($companyName) || empty($clientName)) {
                            continue;
                        }

                        // Skip if data looks like boolean values or invalid data
                        if (in_array(strtoupper($companyName), ['TRUE', 'FALSE', '1', '0']) ||
                            in_array(strtoupper($clientName), ['TRUE', 'FALSE', '1', '0'])) {
                            continue;
                        }

                        $clientDetails[] = [
                            'company_name' => $companyName,
                            'client_name' => $clientName,
                            'meeting_date' => !empty(trim($row[2] ?? '')) ? trim($row[2]) : null,
                            'proposal' => in_array(trim($row[3] ?? ''), ['Yes', 'No']) ? trim($row[3]) : 'No',
                            'link_mockup' => trim($row[4] ?? ''),
                            'status' => in_array(trim($row[5] ?? ''), ['Deal', 'Cancel', 'Progress']) ? trim($row[5]) : 'Progress',
                            'notes' => trim($row[6] ?? ''),
                        ];
                    }

                    // Set the form data
                    $this->form->fill([
                        'client_details' => $clientDetails,
                    ]);

                    Notification::make()
                        ->title('Import successful')
                        ->success()
                        ->body(count($clientDetails) . ' client details imported. Click "Create" to save.')
                        ->send();
                }),
        ];
    }
}
