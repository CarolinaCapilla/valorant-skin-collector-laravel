<?php

namespace App\Http\Controllers;

use App\Services\ValorantService;
use Illuminate\Http\Request;

class SkinsController extends Controller
{
	protected ValorantService $service;

	public function __construct(ValorantService $valorantService)
	{
		$this->service = $valorantService;
	}

	public function index(Request $request)
	{
		try {
			$data = $this->service->fetchAll();
		} catch (\RuntimeException) {
			return response()->json(['message' => 'Valorant API unavailable'], 503);
		}

		// Pagination for progressive loading
		$perPage = (int) $request->get('perPage', 300);
		$page = max(1, (int) $request->get('page', 1));
		$total = count($data);
		$start = ($page - 1) * $perPage;
		$slice = array_slice($data, $start, $perPage);

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
