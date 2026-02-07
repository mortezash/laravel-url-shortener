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
		$days = $request->input('expire_days', 30);

		// چک لینک تکراری
		$url = Url::where('original_url', $request->url)->first();
		if ($url) {
			// ویرایش تاریخ انقضا
			$url->expires_at = now()->addDays($days);
			$url->save();
			return response()->json([
				'short_url' => url($url->short_code),
				'message' => 'URL already exists, expiration updated'
			], 200);
		}

		// تولید کد یکتا
		do {
			$code = Str::random(6);
		} while (Url::where('short_code',$code)->exists());

		// ایجاد رکورد جدید
		$url = Url::create([
			'original_url' => $request->url,
			'short_code' => $code,
			'expires_at' => now()->addDays($days)
		]);

		return response()->json([
			'short_url' => url($code)
		], 201);
	}

	public function redirect($code)
	{
		$url = Url::where('short_code', $code)->first();

		if (!$url) {
			return response()->json(['message'=>'Not found'],404);
		}

		if ($url->expires_at && now()->gt($url->expires_at)) {
			return response()->json(['message'=>'Link expired'],410);
		}

		$url->increment('clicks');

		return redirect($url->original_url, 301);
	}

	public function index()
	{
		$urls = Url::select('*')
			->orderByDesc('created_at')
			->get();

		return response()->json($urls);
	}

	public function destroy($id)
	{
		$url = Url::find($id);

		if (!$url) {
			return response()->json(['message'=>'Not found'],404);
		}

		$url->delete();

		return response()->json(['message'=>'Deleted']);
	}
}
