<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file_path'])) {
            $filePath = $data['file_path'];

            $data['file_name'] = basename($filePath);
            $data['file_size'] = Storage::disk('public')->size($filePath);
            $data['mime_type'] = Storage::disk('public')->mimeType($filePath);
        }

        return $data;
    }
}
