<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentClientsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()
                    ->latest()
                    ->limit(5)
            )
            ->heading('Recent Clients')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->judul),

                Tables\Columns\TextColumn::make('total_clients')
                    ->label('Total Klien')
                    ->state(function ($record) {
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        return count($details);
                    })
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('view_data')
                    ->label('View Data')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Client $record): string => route('clients.show', $record->slug))
                    ->openUrlInNewTab(true)
                    ->color('primary'),
            ]);
    }
}
