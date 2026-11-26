<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot');
    }

    public function store(Request $request): View
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        $demoUrl = null;

        if ($user) {
            $token = Password::broker()->createToken($user);
            $demoUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
            Mail::to($user->email)->send(new ResetPasswordMail($user, $demoUrl));
        }

        return view('auth.forgot-sent', [
            'demoUrl' => app()->environment('local') ? $demoUrl : null,
        ]);
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password updated. Sign in with the new one.')
            : back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
