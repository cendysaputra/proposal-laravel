<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/media/{media}/download', function (\App\Models\Media $media) {
    return Storage::disk('public')->download($media->file_path, $media->file_name);
})->middleware('auth')->name('media.download');
