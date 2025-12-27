<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Filament\Resources\ProposalResource\RelationManagers;
use App\Models\Proposal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Administration';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-proposals');
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('create-proposals');
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('edit-proposals');
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('delete-proposals');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-proposals');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Main Content Area (Left Side)
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Proposal Information')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Proposal')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('regenerate')
                                            ->icon('heroicon-m-arrow-path')
                                            ->tooltip('Regenerate slug from title')
                                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                                $title = $get('title');
                                                if ($title) {
                                                    $set('slug', Str::slug($title));
                                                }
                                            })
                                    )
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Sidebar (Right Side)
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Actions')
                            ->schema([
                                Forms\Components\Placeholder::make('sidebar_placeholder')
                                    ->label('')
                                    ->content('Sidebar content will be customized here'),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('save')
                                        ->label(fn (string $operation) => $operation === 'create' ? 'Create' : 'Save Changes')
                                        ->submit('save')
                                        ->color('primary')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->button(),

                                    Forms\Components\Actions\Action::make('cancel')
                                        ->label('Cancel')
                                        ->url(fn () => ProposalResource::getUrl('index'))
                                        ->color('gray')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->button(),
                                ])
                                    ->fullWidth()
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->url(fn ($record) => ProposalResource::getUrl('edit', ['record' => $record])),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
