<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where('name', 'like', $term)->orWhere('email', 'like', $term);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:customer,admin'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $active = $request->boolean('is_active');

        if ($user->is($request->user()) && ($data['role'] !== 'admin' || ! $active)) {
            return back()->withErrors([
                'user' => 'You cannot pause or demote the account you are using.',
            ]);
        }

        if ($user->isAdmin() && $data['role'] !== 'admin' && User::query()->where('role', 'admin')->count() === 1) {
            return back()->withErrors([
                'user' => 'Keep at least one editor on the desk.',
            ]);
        }

        $user->update([
            'role' => $data['role'],
            'is_active' => $active,
        ]);

        return back()->with('status', $user->name.' updated.');
    }
}
