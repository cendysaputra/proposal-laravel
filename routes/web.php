<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/media/{media}/download', function (\App\Models\Media $media) {
    return Storage::disk('public')->download($media->file_path, $media->file_name);
})->middleware('auth')->name('media.download');

// Invoice Routes
Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/{slug}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{slug}/pdf', [\App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
Route::get('/invoices/{slug}/preview', [\App\Http\Controllers\InvoiceController::class, 'previewPdf'])->name('invoices.preview');
Route::post('/invoices/{slug}/unlock', [\App\Http\Controllers\InvoiceController::class, 'unlock'])->name('invoices.unlock');

// Proposal Routes
Route::get('/proposals', [\App\Http\Controllers\ProposalController::class, 'index'])->name('proposals.index');
Route::get('/proposals/{slug}', [\App\Http\Controllers\ProposalController::class, 'show'])->name('proposals.show');
Route::post('/proposals/{slug}/unlock', [\App\Http\Controllers\ProposalController::class, 'unlock'])->name('proposals.unlock');

// Client Routes
Route::get('/clients', [\App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
Route::get('/clients/{slug}', [\App\Http\Controllers\ClientController::class, 'show'])->name('clients.show');
