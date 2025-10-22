<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ValorantService
{
	protected string $endpoint = 'https://valorant-api.com/v1/weapons/skins';

	public function fetchAll(): array
	{
		return Cache::remember('valorant:skins', 3600, function () {
			$res = Http::timeout(10)->get($this->endpoint);

			if (!$res->successful()) {
				throw new \RuntimeException('Failed to fetch Valorant API');
			}

			$data = $res->json('data') ?? [];

			// Filter out "Standard" skins
			$filtered = array_values(array_filter(
				$data,
				fn($skin) =>
				!str_contains(strtolower($skin['displayName'] ?? ''), 'standard')
			));

			return array_map(fn($skin) => [
				'uuid'       => $skin['uuid'] ?? '',
				'name'       => $skin['displayName'] ?? 'Unknown',
				'image_url'  => $skin['displayIcon'] ?? ($skin['levels'][0]['displayIcon'] ?? ''),
				'weapon'     => $skin['weaponUuid'] ?? '',
				'collection' => $skin['themeUuid'] ?? '',
				'tier'       => ($tierUuid = $skin['contentTierUuid'] ?? '')
					? ['uuid' => $tierUuid]
					:  null,
				'tier_id' => $tierUuid,
				'levels'  => array_map(fn($lv) => [
					'uuid'          => $lv['uuid'] ?? null,
					'displayName'   => $lv['displayName'] ?? null,
					'displayIcon'   => $lv['displayIcon'] ?? null,
					'streamedVideo' => $lv['streamedVideo'] ?? null,
					'levelItem'     => $lv['levelItem'] ?? null,
				], $skin['levels'] ?? []),
				'chromas' => array_map(fn($ch) => [
					'uuid'          => $ch['uuid'] ?? null,
					'displayName'   => $ch['displayName'] ?? null,
					'displayIcon'   => $ch['displayIcon'] ?? null,
					'fullRender'    => $ch['fullRender'] ?? null,
					'swatch'        => $ch['swatch'] ?? null,
					'streamedVideo' => $ch['streamedVideo'] ?? null,
				], $skin['chromas'] ?? []),
			], $filtered);
		});
	}
}
