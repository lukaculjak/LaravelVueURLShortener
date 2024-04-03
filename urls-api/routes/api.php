<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlDictController;

Route::get('/user', function (Request $request) {
    return "USER";
});

Route::get('/urls', [UrlDictController::class, 'get']);
Route::post('/urls', [UrlDictController::class, 'create']);
// Route::get('/urls/{id}', [UrlDictController::class, 'get_by_id'])->name('urls.get_by_id');

Route::get('/urls/{shortenedUrl}', [UrlDictController::class, 'redirectShortenedUrl']);
