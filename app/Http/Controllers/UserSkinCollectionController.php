<?php

namespace App\Http\Controllers;

use App\Models\UserSkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSkinCollectionController extends Controller
{
	public function index(Request $request)
	{
		$user = Auth::user();

		$query = UserSkin::where('user_id', $user->id)->orderBy('created_at', 'desc');

		// optional filters: owned or wishlist
		if ($request->has('owned')) {
			$query->where('owned', (bool) $request->get('owned'));
		}
		if ($request->has('wishlist')) {
			$query->where('wishlist', (bool) $request->get('wishlist'));
		}

		$data = $query->get();

		return response()->json(['data' => $data]);
	}

	public function store(Request $request)
	{
		$user = $request->user();

		$data = $request->validate([
			'skin_uuid'   => 'required|string',
			'chroma_uuid' => 'nullable|string',
			'level_uuid'  => 'nullable|string',
			'owned'       => 'boolean',
			'wishlist'    => 'boolean',
			'metadata'    => 'nullable|array',
		]);

		// upsert by unique key (user_id, skin_uuid, chroma_uuid)
		$entry = UserSkin::updateOrCreate(
			[
				'user_id'     => $user->id,
				'skin_uuid'   => $data['skin_uuid'],
				'chroma_uuid' => $data['chroma_uuid'] ?? null,
			],
			[
				'level_uuid' => $data['level_uuid'] ?? null,
				'owned'      => $data['owned'] ?? true,
				'wishlist'   => $data['wishlist'] ?? false,
				'metadata'   => $data['metadata'] ?? null,
			]
		);

		return response()->json($entry, 201);
	}

	public function destroy(Request $request, $id)
	{
		$user = $request->user();

		$entry = UserSkin::where('id', $id)->where('user_id', $user->id)->firstOrFail();

		$entry->delete();

		return response()->json(['message' => 'Deleted']);
	}
}
