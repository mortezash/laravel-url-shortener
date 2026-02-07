<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUrlRequest;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlController extends Controller
{
	public function store(StoreUrlRequest $request)
	{
		do {
			$code = Str::random(6);
		} while (Url::where('short_code',$code)->exists());

		$url = Url::create([
			'original_url' => $request->url,
			'short_code' => $code,
			'expires_at' => now()->addDays(30)
		]);

		return response()->json([
			'short_url' => url($code)
		], 201);
	}
}
