<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::forShelf()
            ->whereHas('wishlists', fn ($query) => $query->where('user_id', $request->user()->id))
            ->orderByDesc('published_at')
            ->get();

        return view('account.wishlist', ['books' => $books]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $existing = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->where('book_id', $data['book_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from your list.';
        } else {
            Wishlist::create([
                'user_id' => $request->user()->id,
                'book_id' => $data['book_id'],
            ]);
            $message = 'Saved to your list.';
        }

        return back()->with('status', $message);
    }
}
