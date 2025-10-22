<?php

namespace App\Http\Controllers;

use App\Models\UserSkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSkinCollectionController extends Controller
{
	public function index(Request $request)
	{
		$query = UserSkin::where('user_id', Auth::id())->orderBy('created_at', 'desc');

		if ($request->has('owned')) {
			$query->where('owned', (bool) $request->get('owned'));
		}
		if ($request->has('wishlist')) {
			$query->where('wishlist', (bool) $request->get('wishlist'));
		}

		return response()->json(['data' => $query->get()]);
	}

	public function store(Request $request)
	{
		$data = $request->validate([
			'skin_uuid'             => 'required|string',
			'favorite_chroma_uuid'  => 'nullable|string',
			'owned'                 => 'boolean',
			'wishlist'              => 'boolean',
		]);

		// Build metadata JSON
		$metadata = [];
		if (!empty($data['favorite_chroma_uuid'])) {
			$metadata['favorite_chroma_uuid'] = $data['favorite_chroma_uuid'];
		}

		$entry = UserSkin::updateOrCreate(
			[
				'user_id'   => Auth::id(),
				'skin_uuid' => $data['skin_uuid'],
			],
			[
				'owned'    => $data['owned'] ?? true,
				'wishlist' => $data['wishlist'] ?? false,
				'metadata' => !empty($metadata) ? $metadata : null,
			]
		);

		return response()->json($entry, 201);
	}

	public function destroyBySkin(Request $request)
	{
		$skinUuid = $request->query('skin_uuid') ?? $request->input('skin_uuid');

		if (!$skinUuid) {
			return response()->json(['message' => 'skin_uuid is required'], 400);
		}

		$deleted = UserSkin::where('user_id', Auth::id())
			->where('skin_uuid', $skinUuid)
			->delete();

		return response()->json(['message' => 'Deleted', 'count' => $deleted]);
	}

	public function addToWishlist(Request $request)
	{
		$data = $request->validate([
			'skin_uuid'            => 'required|string',
			'favorite_chroma_uuid' => 'nullable|string',
		]);

		// Build metadata JSON
		$metadata = [];
		if (!empty($data['favorite_chroma_uuid'])) {
			$metadata['favorite_chroma_uuid'] = $data['favorite_chroma_uuid'];
		}

		$entry = UserSkin::updateOrCreate(
			[
				'user_id'   => Auth::id(),
				'skin_uuid' => $data['skin_uuid'],
			],
			[
				'wishlist' => true,
				'metadata' => !empty($metadata) ? $metadata : null,
			]
		);

		return response()->json($entry, 201);
	}

	public function removeFromWishlist(Request $request)
	{
		$skinUuid = $request->query('skin_uuid') ?? $request->input('skin_uuid');

		if (!$skinUuid) {
			return response()->json(['message' => 'skin_uuid is required'], 400);
		}

		$updated = UserSkin::where('user_id', Auth::id())
			->where('skin_uuid', $skinUuid)
			->update(['wishlist' => false]);

		return response()->json(['message' => 'Removed from wishlist', 'updated' => $updated]);
	}

	public function updateFavoriteChroma(Request $request)
	{
		$data = $request->validate([
			'skin_uuid'            => 'required|string',
			'favorite_chroma_uuid' => 'required|string',
		]);

		$userSkin = UserSkin::where('user_id', Auth::id())
			->where('skin_uuid', $data['skin_uuid'])
			->first();

		if (!$userSkin) {
			return response()->json(['message' => 'Skin not found in collection'], 404);
		}

		// Update metadata with new favorite chroma
		$metadata = $userSkin->metadata ?? [];
		$metadata['favorite_chroma_uuid'] = $data['favorite_chroma_uuid'];

		$userSkin->metadata = $metadata;
		$userSkin->save();

		return response()->json($userSkin);
	}
}
