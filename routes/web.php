<?php

use Illuminate\Support\Facades\Route;

Route::get('{code}', [\App\Http\Controllers\Api\UrlController::class, 'redirect']);
