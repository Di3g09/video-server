<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaItemController;
use App\Http\Controllers\PlaylistController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // preview de video
    Route::get('media-items/{mediaItem}/preview', [MediaItemController::class, 'preview'])
        ->name('media-items.preview');

    Route::resource('media-items', MediaItemController::class)->except(['show']);
    
    Route::resource('playlists', PlaylistController::class)->except(['show']);
    
    Route::get('playlists/{playlist}/items', [PlaylistController::class, 'editItems'])
        ->name('playlists.items.edit');

    Route::put('playlists/{playlist}/items', [PlaylistController::class, 'updateItems'])
        ->name('playlists.items.update');
});

require __DIR__.'/auth.php';
