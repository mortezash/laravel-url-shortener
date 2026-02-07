<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUrlRequest;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "URLs", description: "Operations about URLs")]
#[OA\Info(
	title: "Laravel URL Shortener API",
	version: "1.0.0",
	description: "مستندات API کوتاه‌کننده لینک"
)]
class UrlController extends Controller
{
	#[OA\Post(
		path: "/api/v1/shorten",
		tags: ["URLs"],
		summary: "Create a new short URL",
		requestBody: new OA\RequestBody(
			required: true,
			content: [
				new OA\JsonContent(
					required: ["url", "expire_days"],
					properties: [
						new OA\Property(property: "url", type: "string", example: "https://google.com"),
						new OA\Property(property: "expire_days", type: "integer", example: 30)
					]
				)
			]
		),
		responses: [
			new OA\Response(response: 200, description: "URL updated if exists"),
			new OA\Response(response: 201, description: "New URL created successfully"),
			new OA\Response(response: 422, description: "Validation error")
		]
	)]
	public function store(StoreUrlRequest $request)
	{
		$days = $request->input('expire_days', 30);

		// چک لینک تکراری
		$url = Url::where('original_url', $request->url)->first();
		if ($url) {
			// ویرایش تاریخ انقضا
			$url->expires_at = now()->addDays($days);
			$url->save();

			return new UrlResource($url);
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

		return new UrlResource($url);
	}

	#[OA\Get(
		path: "/{code}",
		tags: ["URLs"],
		summary: "Redirect to the original URL",
		parameters: [
			new OA\Parameter(
				name: "code",
				in: "path",
				description: "Short code",
				required: true,
				schema: new OA\Schema(type: "string")
			)
		],
		responses: [
			new OA\Response(response: 301, description: "Redirects to original URL"),
			new OA\Response(response: 404, description: "URL not found"),
			new OA\Response(response: 410, description: "URL expired")
		]
	)]
	public function redirect(string $code)
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

	#[OA\Get(
		path: "/api/v1/urls",
		tags: ["URLs"],
		summary: "List URLs with pagination",
		responses: [
			new OA\Response(response: 200, description: "Paginated list of URLs")
		]
	)]
	public function index()
	{
		$urls = Url::withTrashed()
			->orderByDesc('created_at')
			->paginate(10);

		return UrlResource::collection($urls);
	}

	#[OA\Delete(
		path: "/api/v1/urls/{id}",
		tags: ["URLs"],
		summary: "Soft delete a URL",
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				schema: new OA\Schema(type: "integer"),
				description: "URL ID"
			)
		],
		responses: [
			new OA\Response(response: 200, description: "URL soft deleted successfully"),
			new OA\Response(response: 404, description: "URL not found")
		]
	)]
	public function destroy(int $id)
	{
		$url = Url::find($id);

		if (!$url) {
			return response()->json(['message'=>'Not found'],404);
		}

		$url->delete();

		return response()->json(['message' => 'URL soft deleted successfully']);
	}

	#[OA\Post(
		path: "/api/v1/urls/{id}/restore",
		tags: ["URLs"],
		summary: "Restore a soft-deleted URL",
		parameters: [
			new OA\Parameter(
				name: "id",
				in: "path",
				required: true,
				schema: new OA\Schema(type: "integer"),
				description: "URL ID"
			)
		],
		responses: [
			new OA\Response(response: 200, description: "URL restored successfully"),
			new OA\Response(response: 404, description: "URL not found"),
			new OA\Response(response: 400, description: "URL is not deleted")
		]
	)]
	public function restore(int $id)
	{
		// پیدا کردن لینک حتی اگر حذف شده باشد
		$url = Url::withTrashed()->find($id);

		if (!$url) {
			return response()->json(['message' => 'Not found'], 404);
		}

		// اگر حذف نشده بود
		if (!$url->trashed()) {
			return response()->json(['message' => 'URL is not deleted'], 400);
		}

		// بازگرداندن
		$url->restore();

		return response()->json([
			'message' => 'URL restored successfully',
			'short_url' => url($url->short_code)
		], 200);
	}
}
