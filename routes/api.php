<?php
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
	Route::post('shorten', [App\Http\Controllers\Api\UrlController::class, 'store']);
});