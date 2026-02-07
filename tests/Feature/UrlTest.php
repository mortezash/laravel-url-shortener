<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Url;

class UrlTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_shorten_a_new_url()
	{
		$new_url ='https://google.com';
		$response = $this->postJson('/api/v1/shorten', [
			'url' => $new_url,
			'expire_days' => 10
		]);

		$response->assertStatus(201)
			->assertJsonPath('data.original_url', $new_url);

		$this->assertDatabaseHas('urls', [
			'original_url' => $new_url
		]);
	}

	public function test_updates_expire_days_if_url_exists()
	{
		$url = Url::factory()->create([
			'original_url' => 'https://google.com',
			'short_code' => 'abc123',
			'expires_at' => now()->addDays(5)
		]);

		$this->postJson('/api/v1/shorten', [
			'url' => 'https://google.com',
			'expire_days' => 15
		])->assertStatus(200);

		$url->refresh();
		$this->assertTrue($url->expires_at->gt(now()->addDays(10)));
	}

	public function test_redirects_and_increments_clicks()
	{
		$url = Url::factory()->create([
			'original_url' => 'https://google.com',
			'short_code' => 'abc123',
			'clicks' => 0
		]);

		$this->get('/'.$url->short_code)
			->assertStatus(301)
			->assertRedirect('https://google.com');

		$url->refresh();
		$this->assertEquals(1, $url->clicks);
	}

	public function test_can_list_urls_with_pagination()
	{
		Url::factory()->count(15)->create();

		$response = $this->getJson('/api/v1/urls');

		$response->assertStatus(200)
			->assertJsonStructure([
				'data',
				'links',
				'meta'
			]);

		$response->assertJsonPath('meta.per_page', 10);
	}

	public function test_can_soft_delete_a_url()
	{
		$url = Url::factory()->create();

		$this->deleteJson('/api/v1/urls/'.$url->id)
			->assertStatus(200);

		$this->assertSoftDeleted('urls', ['id' => $url->id]);
	}

	public function test_can_restore_a_soft_deleted_url()
	{
		$url = Url::factory()->create();
		$url->delete();

		$this->postJson('/api/v1/urls/'.$url->id.'/restore')
			->assertStatus(200)
			->assertJsonStructure([
				'message',
				'short_url'
			]);

		$this->assertDatabaseHas('urls', [
			'id' => $url->id,
			'deleted_at' => null
		]);
	}

	public function test_returns_404_for_nonexistent_url()
	{
		$this->deleteJson('/api/v1/urls/999')->assertStatus(404);
		$this->postJson('/api/v1/urls/999/restore')->assertStatus(404);
		$this->get('/nonexistent')->assertStatus(404);
	}
}
