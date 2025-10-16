<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ValorantService
{
	protected string $endpoint = 'https://valorant-api.com/v1/weapons/skins';

	/**
	 * Fetch and cache transformed skins from Valorant API.
	 * @return array
	 * @throws \RuntimeException
	 */
	public function fetchAll(): array
	{
		// cache for 1 hour
		return Cache::store('redis')->remember('valorant:skins', 3600, function () {
			$res = Http::timeout(10)->get($this->endpoint);

			if (! $res->successful()) {
				throw new \RuntimeException('Failed to fetch Valorant API');
			}

			$data = $res->json('data') ?? [];

			// Filter out skins with "Standard" in the displayName (case-insensitive)
			$filtered = array_values(array_filter($data, function ($it) {
				$name = strtolower($it['displayName'] ?? '');
				return !str_contains($name, 'standard');
			}));

			// Map to our simplified shape
			return array_map(function ($it) {
				$skinUuid = $it['uuid'] ?? '';
				$image = $it['displayIcon'] ?? ($it['levels'][0]['displayIcon'] ?? '');
				$tierUuid = $it['contentTierUuid'] ?? '';

				return [
					'uuid' => $skinUuid,
					'name' => $it['displayName'] ?? 'Unknown',
					'image_url' => $image,
					'weapon' => $it['weaponUuid'] ?? '',
					'collection' => $it['themeUuid'] ?? '',
					'tier' => $tierUuid ? ['uuid' => $tierUuid] : null,
					'tier_id' => $tierUuid,
					'levels' => array_map(function ($lv) {
						return [
							'uuid' => $lv['uuid'] ?? null,
							'displayName' => $lv['displayName'] ?? null,
							'displayIcon' => $lv['displayIcon'] ?? null,
							'streamedVideo' => $lv['streamedVideo'] ?? null,
							'levelItem' => $lv['levelItem'] ?? null,
						];
					}, $it['levels'] ?? []),
					'chromas' => array_map(function ($ch) {
						return [
							'uuid' => $ch['uuid'] ?? null,
							'displayName' => $ch['displayName'] ?? null,
							'displayIcon' => $ch['displayIcon'] ?? null,
							'fullRender' => $ch['fullRender'] ?? null,
							'swatch' => $ch['swatch'] ?? null,
							'streamedVideo' => $ch['streamedVideo'] ?? null,
						];
					}, $it['chromas'] ?? []),
				];
			}, $filtered);
		});
	}
}
