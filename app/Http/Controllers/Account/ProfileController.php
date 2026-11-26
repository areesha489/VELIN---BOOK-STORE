<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{8,20}$/'],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check((string) $request->input('current_password'), $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'The current password does not match.',
                ]);
            }

            $user->password = $data['password'];
        }

        $user->save();

        return back()->with('status', 'Profile updated.');
    }
}
