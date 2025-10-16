<?php

namespace App\Http\Controllers;

use App\Services\ValorantService;
use Illuminate\Http\Request;

class SkinsController extends Controller
{
	protected ValorantService $service;

	public function __construct(ValorantService $service)
	{
		$this->service = $service;
	}

	public function index(Request $request)
	{
		try {
			$all = $this->service->fetchAll();
		} catch (\RuntimeException $e) {
			return response()->json(['message' => 'Valorant API unavailable'], 503);
		}

		// apply filters similar to frontend
		$weapon = $request->get('weapon');
		$collection = $request->get('collection');
		$tier = $request->get('tier');
		$query = strtolower(trim((string) ($request->get('search') ?? '')));

		$filtered = array_values(array_filter($all, function ($s) use ($weapon, $collection, $tier, $query) {
			if ($weapon && ($s['weapon'] ?? '') !== $weapon) return false;
			if ($collection && ($s['collection'] ?? '') !== $collection) return false;
			if ($tier && ($s['tier_id'] ?? '') !== $tier) return false;
			if ($query && stripos(($s['name'] ?? ''), $query) === false) return false;
			return true;
		}));

		// pagination
		$perPage = (int) $request->get('perPage', 20);
		$page = max(1, (int) $request->get('page', 1));
		$total = count($filtered);
		$start = ($page - 1) * $perPage;
		$slice = array_slice($filtered, $start, $perPage);

		return response()->json([
			'data' => $slice,
			'meta' => [
				'total' => $total,
				'page' => $page,
				'perPage' => $perPage,
				'totalPages' => (int) ceil($total / $perPage),
			],
		]);
	}
}
