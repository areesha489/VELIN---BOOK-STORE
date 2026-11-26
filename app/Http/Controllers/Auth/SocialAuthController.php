<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function store(Request $request, string $provider, CartService $carts): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);
        abort_unless(in_array($provider, ['google', 'github'], true), 404);

        if (config("services.{$provider}.client_id")) {
            return redirect()->route('login')->withErrors([
                'email' => 'OAuth keys are set for '.ucfirst($provider).'. Install Laravel Socialite and wire the redirect before using a live provider.',
            ]);
        }

        $profiles = [
            'google' => ['Aanya Deshpande', 'aanya.google@velin.store'],
            'github' => ['Kabir Shah', 'kabir.github@velin.store'],
        ];

        [$name, $email] = $profiles[$provider];

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Str::password(32),
                'role' => 'customer',
                'provider' => $provider,
                'provider_id' => 'demo-'.$provider,
                'is_active' => true,
            ]
        );

        if ($user->wasRecentlyCreated) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'email' => 'This social profile has been paused.',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $carts->mergeIntoUser($user);

        return redirect()->intended(route('home'))->with('status', 'Signed in as '.$user->name.' through the '.ucfirst($provider).' classroom profile.');
    }
}
