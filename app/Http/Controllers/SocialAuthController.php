<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
	public function redirect($provider)
	{
		// stateless for SPA or issues with sessions during redirect
		return Socialite::driver($provider)->redirect();
	}

	public function callback(Request $request, $provider)
	{
		// retrieve user from provider
		$socialUser = Socialite::driver($provider)->user();

		// find or create user
		$user = User::firstOrCreate(
			['provider' => $provider, 'provider_id' => $socialUser->getId()],
			[
				'email' => $socialUser->getEmail(),
				'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Unknown',
				'password' => bcrypt(Str::random(24)), // random password
			]
		);

		// login and create session cookie for Sanctum
		auth()->login($user);

		// return small HTML view that posts message to opener (SPA popup)
		return view('oauth.callback', ['user' => $user]);
	}
}
