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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Imports\ClientImporter;

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
                                    ->collapsed()
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
                                Forms\Components\Placeholder::make('status_statistics')
                                    ->label('Status Statistics')
                                    ->content(function ($record) {
                                        if (!$record || !$record->client_details) {
                                            return new \Illuminate\Support\HtmlString('
                                                <div class="text-sm text-gray-500">No data available</div>
                                            ');
                                        }

                                        $details = is_array($record->client_details) ? $record->client_details : [];

                                        $dealCount = 0;
                                        $progressCount = 0;
                                        $cancelCount = 0;
                                        $proposalYesCount = 0;
                                        $mockupCount = 0;

                                        foreach ($details as $detail) {
                                            $status = $detail['status'] ?? '';
                                            switch ($status) {
                                                case 'Deal':
                                                    $dealCount++;
                                                    break;
                                                case 'Progress':
                                                    $progressCount++;
                                                    break;
                                                case 'Cancel':
                                                    $cancelCount++;
                                                    break;
                                            }

                                            // Count Proposal Yes
                                            if (($detail['proposal'] ?? '') === 'Yes') {
                                                $proposalYesCount++;
                                            }

                                            // Count Mockup (if link_mockup is not empty)
                                            if (!empty(trim($detail['link_mockup'] ?? ''))) {
                                                $mockupCount++;
                                            }
                                        }

                                        $total = count($details);

                                        $dealPercent = $total > 0 ? round(($dealCount / $total * 100), 1) : 0;
                                        $progressPercent = $total > 0 ? round(($progressCount / $total * 100), 1) : 0;
                                        $cancelPercent = $total > 0 ? round(($cancelCount / $total * 100), 1) : 0;
                                        $proposalYesPercent = $total > 0 ? round(($proposalYesCount / $total * 100), 1) : 0;
                                        $mockupPercent = $total > 0 ? round(($mockupCount / $total * 100), 1) : 0;

                                        return new \Illuminate\Support\HtmlString('
                                            <div class="space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Deal</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">' . $dealCount . '</span>
                                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">' . $dealPercent . '%</span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: ' . $dealPercent . '%"></div>
                                                </div>

                                                <div class="flex items-center justify-between mt-4">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20">' . $progressCount . '</span>
                                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">' . $progressPercent . '%</span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                    <div class="bg-yellow-500 h-2.5 rounded-full" style="width: ' . $progressPercent . '%"></div>
                                                </div>

                                                <div class="flex items-center justify-between mt-4">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cancel</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">' . $cancelCount . '</span>
                                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">' . $cancelPercent . '%</span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                    <div class="bg-red-600 h-2.5 rounded-full" style="width: ' . $cancelPercent . '%"></div>
                                                </div>

                                                <div class="flex items-center justify-between mt-4">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Proposal</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-600/20">' . $proposalYesCount . '</span>
                                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">' . $proposalYesPercent . '%</span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                    <div class="bg-purple-600 h-2.5 rounded-full" style="width: ' . $proposalYesPercent . '%"></div>
                                                </div>

                                                <div class="flex items-center justify-between mt-4">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mockup</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20">' . $mockupCount . '</span>
                                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">' . $mockupPercent . '%</span>
                                                    </div>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: ' . $mockupPercent . '%"></div>
                                                </div>

                                                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Total Clients Masuk</span>
                                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-500/10">' . $total . '</span>
                                                    </div>
                                                </div>
                                            </div>
                                        ');
                                    }),
                            ]),

                        Forms\Components\Section::make('Data Client Filter')
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Bulan')
                                    ->options([
                                        'Januari' => 'Januari',
                                        'Februari' => 'Februari',
                                        'Maret' => 'Maret',
                                        'April' => 'April',
                                        'Mei' => 'Mei',
                                        'Juni' => 'Juni',
                                        'Juli' => 'Juli',
                                        'Agustus' => 'Agustus',
                                        'September' => 'September',
                                        'Oktober' => 'Oktober',
                                        'November' => 'November',
                                        'Desember' => 'Desember',
                                    ])
                                    ->native(false)
                                    ->placeholder('Pilih Bulan'),

                                Forms\Components\CheckboxList::make('years')
                                    ->label('Tahun')
                                    ->relationship('years', 'year')
                                    ->options(fn () => \App\Models\Year::orderBy('order')->pluck('year', 'id'))
                                    ->extraAttributes([
                                        'class' => 'max-h-48 overflow-y-auto pr-2',
                                    ]),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('manageYears')
                                        ->label('Kelola Tahun')
                                        ->url(fn () => route('filament.admin.resources.years.index'))
                                        ->openUrlInNewTab()
                                        ->color('gray')
                                        ->size('sm'),
                                ]),
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
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('progress_count')
                    ->label('Progress')
                    ->state(function ($record) {
                        if (!$record->client_details) return '0 (0%)';
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        $total = count($details);
                        if ($total === 0) return '0 (0%)';
                        $count = collect($details)->where('status', 'Progress')->count();
                        $percentage = round(($count / $total) * 100, 1);
                        return $count . ' (' . $percentage . '%)';
                    })
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('deal_count')
                    ->label('Deal')
                    ->state(function ($record) {
                        if (!$record->client_details) return '0 (0%)';
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        $total = count($details);
                        if ($total === 0) return '0 (0%)';
                        $count = collect($details)->where('status', 'Deal')->count();
                        $percentage = round(($count / $total) * 100, 1);
                        return $count . ' (' . $percentage . '%)';
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('cancel_count')
                    ->label('Cancel')
                    ->state(function ($record) {
                        if (!$record->client_details) return '0 (0%)';
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        $total = count($details);
                        if ($total === 0) return '0 (0%)';
                        $count = collect($details)->where('status', 'Cancel')->count();
                        $percentage = round(($count / $total) * 100, 1);
                        return $count . ' (' . $percentage . '%)';
                    })
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('proposal_count')
                    ->label('Proposal')
                    ->state(function ($record) {
                        if (!$record->client_details) return '0 (0%)';
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        $total = count($details);
                        if ($total === 0) return '0 (0%)';
                        $count = collect($details)->where('proposal', 'Yes')->count();
                        $percentage = round(($count / $total) * 100, 1);
                        return $count . ' (' . $percentage . '%)';
                    })
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('mockup_count')
                    ->label('Mockup')
                    ->state(function ($record) {
                        if (!$record->client_details) return '0 (0%)';
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        $total = count($details);
                        if ($total === 0) return '0 (0%)';
                        $count = collect($details)->filter(function ($detail) {
                            return !empty(trim($detail['link_mockup'] ?? ''));
                        })->count();
                        $percentage = round(($count / $total) * 100, 1);
                        return $count . ' (' . $percentage . '%)';
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('meeting_count')
                    ->label('Meeting')
                    ->state(function ($record) {
                        if (!$record->client_details) return '0 (0%)';
                        $details = is_array($record->client_details) ? $record->client_details : [];
                        $total = count($details);
                        if ($total === 0) return '0 (0%)';
                        $count = collect($details)->filter(function ($detail) {
                            return !empty($detail['meeting_date'] ?? null);
                        })->count();
                        $percentage = round(($count / $total) * 100, 1);
                        return $count . ' (' . $percentage . '%)';
                    })
                    ->badge()
                    ->color('violet'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export CSV')
                    ->color('primary')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'clients-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'clients-selected-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::CSV),
                        ]),
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
