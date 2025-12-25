<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Administration';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-invoices');
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('create-invoices');
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('edit-invoices');
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('delete-invoices');
    }

    public static function canView($record): bool
    {
        return auth()->check() && auth()->user()->hasPermission('view-invoices');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Invoice Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Invoice Title')
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
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('number_invoice')
                            ->label('Invoice Number')
                            ->required()
                            ->placeholder('Contoh: INV / 001 / XII / 2024 / DP')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('')
                            ->content(new HtmlString('
                                <div class="text-sm">
                                    <p class="font-semibold mb-2">Format: INV / Nomor / Bulan (Romawi) / Tahun / Kode</p>
                                    <p> Kode:</p>
                                    <ul class="list-disc list-inside space-y-1 ml-2">
                                        <li>DP - down payment</li>
                                        <li>LN - pelunasan</li>
                                        <li>REN - perpanjangan</li>
                                        <li>ADD - add-ons / tambahan</li>
                                    </ul>
                                </div>
                            '))
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('invoice_date')
                            ->label('Invoice Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),

                        Forms\Components\DatePicker::make('invoice_due_date')
                            ->label('Invoice Due Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),

                        Forms\Components\Radio::make('brand')
                            ->label('Brand Logo')
                            ->options([
                                'logobrand-1' => 'Logo Brand 1',
                                'logobrand-2' => 'Logo Brand 2',
                            ])
                            ->descriptions([
                                'logobrand-1' => new HtmlString('<img src="' . asset('images/logobrand-1.png') . '" alt="Logo Brand 1" style="max-width: 200px; max-height: 100px; margin-top: 8px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px;">'),
                                'logobrand-2' => new HtmlString('<img src="' . asset('images/logobrand-2.png') . '" alt="Logo Brand 2" style="max-width: 200px; max-height: 100px; margin-top: 8px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px;">'),
                            ])
                            ->default('logobrand-1')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\Textarea::make('client_info')
                            ->label('Client Info')
                            ->placeholder('Enter company name, address, phone, email, etc.')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('item_details')
                            ->label('Invoice Items')
                            ->schema([
                              Forms\Components\TextInput::make('qty')
                            ->label('QTY')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(99999)
                            ->columnSpan(1),
        
                        Forms\Components\TextInput::make('items')
                            ->label('Items / Description')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
        
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->columnSpan(1),
                        ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->addActionLabel('Add Item')
                            ->columnSpanFull()
                            ->live(),

                        Forms\Components\MarkdownEditor::make('additional_info')
                            ->label('Additional Info')
                            ->columnSpanFull(),

                        Forms\Components\MarkdownEditor::make('custom_item_details')
                            ->label('Custom Item Details')
                            ->columnSpanFull(),
                           
                        Forms\Components\TextInput::make('prepared_by')
                            ->label('Prepared By')
                            ->columnSpanFull()
                            ->required(),

                        Forms\Components\Radio::make('paid')
                            ->label('Paid')
                            ->options([
                                0 => 'No',
                                1 => 'Yes',
                            ])
                            ->default(0)
                            ->inline()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Publishing')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'published' => 'Published',
                                        'draft' => 'Draft',
                                    ])
                                    ->default('published')
                                    ->required()
                                    ->native(false)
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->published_at !== null ? 'published' : 'draft');
                                        }
                                    })
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state === 'published') {
                                            // Set to now if publishing and no date set
                                            if (!$get('published_at')) {
                                                $set('published_at', now());
                                            }
                                        } else {
                                            // Clear date if setting to draft
                                            $set('published_at', null);
                                        }
                                    }),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publish Date')
                                    ->native(false)
                                    ->disabled(fn (Forms\Get $get) => $get('status') === 'draft'),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('save')
                                        ->label(fn (string $operation) => $operation === 'create' ? 'Create' : 'Save Changes')
                                        ->submit('save')
                                        ->color('primary')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->button(),

                                    Forms\Components\Actions\Action::make('cancel')
                                        ->label('Cancel')
                                        ->url(fn () => InvoiceResource::getUrl('index'))
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
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
               
                Tables\Columns\TextColumn::make('number_invoice')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('Draft'),

                Tables\Columns\TextColumn::make('client_info')
                    ->label('Client Info')
                    ->searchable()
                    ->limit(40)
                    ->toggleable()
                    ->placeholder('No client info'),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Invoice Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('invoice_due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->invoice_due_date && $record->invoice_due_date->isPast() ? 'danger' : 'success')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('paid')
                    ->label('Paid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('brand')
                    ->label('Brand')
                    ->getStateUsing(fn ($record) => $record->brand ? asset('images/' . $record->brand . '.png') : null)
                    ->square()
                    ->size(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('d M Y')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d M Y') : 'Draft')
                    ->sortable()
                    ->toggleable(),

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
                Tables\Filters\TernaryFilter::make('published_at')
                    ->label('Published')
                    ->nullable()
                    ->placeholder('All invoices')
                    ->trueLabel('Published only')
                    ->falseLabel('Drafts only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
