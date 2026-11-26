<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\AddressBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.addresses', [
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->get(),
        ]);
    }

    public function store(Request $request, AddressBook $book): RedirectResponse
    {
        $book->store($request->user(), $request->all(), $request->boolean('is_default'));

        return back()->with('status', 'Address saved.');
    }

    public function edit(Request $request, Address $address): View
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        return view('account.address-edit', ['address' => $address]);
    }

    public function update(Request $request, Address $address, AddressBook $book): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $book->update($address, $request->all(), $request->boolean('is_default'));

        return redirect()->route('addresses.index')->with('status', 'Address updated.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->first()?->update(['is_default' => true]);
        }

        return back()->with('status', 'Address removed.');
    }
}
