<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
	public function toArray($request): array
	{
		return [
			'id' => $this->id,
			'original_url' => $this->original_url,
			'short_code' => $this->short_code,
			'short_url' => url($this->short_code),
			'clicks' => $this->clicks,
			'expires_at' => $this->expires_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'deleted_at' => $this->deleted_at,
		];
	}
}
