<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Clients';

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Data Clients';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-clients');
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('create-clients');
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('edit-clients');
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('delete-clients');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-clients');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Client Information')
                            ->schema([
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul')
                                    ->placeholder('Bulan dan Tahun')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('client_details')
                                    ->label('Client Details')
                                    ->schema([
                                        // Baris 1: Company Name, Client Name, Meeting Date, Proposal
                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Company Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('client_name')
                                            ->label('Client Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),

                                        Forms\Components\DatePicker::make('meeting_date')
                                            ->label('Meeting Date')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->columnSpan(3),

                                        Forms\Components\Select::make('proposal')
                                            ->label('Proposal')
                                            ->options([
                                                'No' => 'No',
                                                'Yes' => 'Yes',
                                            ])
                                            ->default('No')
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(3),

                                        // Baris 2: Link Mockup, Status
                                        Forms\Components\TextInput::make('link_mockup')
                                            ->label('Link Mockup')
                                            ->url()
                                            ->maxLength(255)
                                            ->columnSpan(8),

                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'Deal' => 'Deal',
                                                'Cancel' => 'Cancel',
                                                'Progress' => 'Progress',
                                                'Review Mockup' => 'Review Mockup',
                                            ])
                                            ->default('Progress')
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(4),

                                        // Baris 3: Notes
                                        Forms\Components\TextInput::make('notes')
                                            ->label('Notes')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(12)
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['company_name'] ?? 'New Client')
                                    ->addActionLabel('Add Client')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Data Information')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn ($record): string => $record ? $record->created_at->diffForHumans() : '-'),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn ($record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul (Bulan & Tahun)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('client_details')
                    ->label('Total Clients')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) . ' client(s)' : '0 client')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
