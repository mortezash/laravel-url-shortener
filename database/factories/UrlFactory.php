<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Url;

class UrlFactory extends Factory
{
	protected $model = Url::class;

	public function definition()
	{
		return [
			'original_url' => $this->faker->url,
			'short_code' => Str::random(6),
			'clicks' => $this->faker->numberBetween(0, 100),
			'expires_at' => $this->faker->optional()->dateTimeBetween('now', '+30 days')
		];
	}
}
