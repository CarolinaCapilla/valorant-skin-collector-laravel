<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
	public function redirect(string $provider)
	{
		if (!in_array($provider, ['google', 'github'])) {
			return redirect(route('login'))->withErrors(['Unsupported provider']);
		}

		return Socialite::driver($provider)->redirect();
	}

	public function callback(string $provider)
	{
		if (!in_array($provider, ['google', 'github'])) {
			return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/login?error=unsupported_provider');
		}

		try {
			$socialUser = Socialite::driver($provider)->user();

			// First try to find user by provider_name + provider_id
			$user = User::where('provider_name', $provider)
				->where('provider_id', $socialUser->getId())
				->first();

			// If not found, try to find by email (user might have signed up with different provider)
			if (!$user) {
				$user = User::where('email', $socialUser->getEmail())->first();
			}

			// If user exists, update their OAuth info
			if ($user) {
				$user->update([
					'provider_name'          => $provider,
					'provider_id'            => $socialUser->getId(),
					'provider_token'         => $socialUser->token,
					'provider_refresh_token' => $socialUser->refreshToken,
				]);
			} else {
				// Create new user
				$user = User::create([
					'provider_name'          => $provider,
					'provider_id'            => $socialUser->getId(),
					'name'                   => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
					'email'                  => $socialUser->getEmail(),
					'provider_token'         => $socialUser->token,
					'provider_refresh_token' => $socialUser->refreshToken,
				]);
			}

			Auth::login($user);
			// Redirect to frontend callback page
			return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/auth/callback');
		} catch (\Exception $e) {
			return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/login?error=authentication_failed');
		}
	}
}
