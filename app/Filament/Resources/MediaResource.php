<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Filament\Resources\MediaResource\RelationManagers;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Media';

    protected static ?string $modelLabel = 'Media';

    protected static ?string $pluralModelLabel = 'Media';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload File')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File')
                            ->disk('public')
                            ->directory('media')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'image/*',
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'video/*',
                                'application/zip',
                                'application/x-rar-compressed',
                            ])
                            ->maxSize(102400)
                            ->image()
                            ->imagePreviewHeight('250')
                            ->imageEditor()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('File Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Display Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('type')
                            ->label('File Type')
                            ->options([
                                'image' => 'Image',
                                'document' => 'Document',
                                'video' => 'Video',
                                'archive' => 'Archive',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('category')
                            ->label('Category/Folder')
                            ->options([
                                'General' => 'General',
                                'Proposals' => 'Proposals',
                                'Invoices' => 'Invoices',
                                'Contracts' => 'Contracts',
                                'Reports' => 'Reports',
                                'Marketing' => 'Marketing',
                                'Others' => 'Others',
                            ])
                            ->searchable()
                            ->native(false)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                        Forms\Components\Hidden::make('file_name'),
                        Forms\Components\Hidden::make('file_size'),
                        Forms\Components\Hidden::make('mime_type'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Preview')
                    ->disk('public')
                    ->size(60)
                    ->defaultImageUrl(fn ($record) => $record->isDocument()
                        ? asset('images/document-icon.png')
                        : null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->file_name)
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'image',
                        'primary' => 'document',
                        'warning' => 'video',
                        'gray' => 'archive',
                    ])
                    ->icons([
                        'heroicon-o-photo' => 'image',
                        'heroicon-o-document-text' => 'document',
                        'heroicon-o-film' => 'video',
                        'heroicon-o-archive-box' => 'archive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($record) => $record->getFileSizeFormatted())
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Uploaded By')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('File Type')
                    ->options([
                        'image' => 'Images',
                        'document' => 'Documents',
                        'video' => 'Videos',
                        'archive' => 'Archives',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'General' => 'General',
                        'Proposals' => 'Proposals',
                        'Invoices' => 'Invoices',
                        'Contracts' => 'Contracts',
                        'Reports' => 'Reports',
                        'Marketing' => 'Marketing',
                        'Others' => 'Others',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('media.download', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'view' => Pages\ViewMedia::route('/{record}'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
