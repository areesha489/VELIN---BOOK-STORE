<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        $book->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data,
        );

        return back()->with('status', 'Your note is on the shelf.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless($review->user_id === $request->user()->id, 403);
        $review->delete();

        return back()->with('status', 'Review removed.');
    }

    public function like(Request $request, Review $review): RedirectResponse
    {
        if ($review->user_id === $request->user()->id) {
            return back()->withErrors([
                'like' => 'You can like another reader’s note.',
            ]);
        }

        $existing = $review->likes()->where('user_id', $request->user()->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', 'Like removed.');
        }

        $review->likes()->create(['user_id' => $request->user()->id]);

        return back()->with('status', 'Liked.');
    }
}
