<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UrlController;

Route::prefix('v1')->group(function () {
	Route::post('shorten', [UrlController::class, 'store']);
	Route::get('/urls', [UrlController::class, 'index']);
	Route::delete('/urls/{id}', [UrlController::class, 'destroy']);
});