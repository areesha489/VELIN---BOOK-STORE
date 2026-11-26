<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReviewLike;
use Illuminate\View\View;

class BookController extends Controller
{
    public function show(Book $book): View
    {
        $book->load(['author', 'genre'])->loadAvg('reviews', 'rating')->loadCount('reviews');

        $reviews = $book->reviews()->with('user:id,name')->withCount('likes')->latest()->get();
        $user = auth()->user();
        $liked = [];
        $savedIds = [];
        $userReview = null;

        if ($user) {
            $userReview = $reviews->firstWhere('user_id', $user->id);
            $liked = ReviewLike::query()
                ->where('user_id', $user->id)
                ->whereIn('review_id', $reviews->pluck('id'))
                ->pluck('review_id')
                ->all();
            $savedIds = $user->wishlists()->pluck('book_id')->all();
        }

        $saved = in_array($book->id, $savedIds, true);

        $related = Book::forShelf()
            ->where('genre_id', $book->genre_id)
            ->whereKeyNot($book->id)
            ->orderByDesc('popularity')
            ->take(4)
            ->get();

        return view('store.book', compact('book', 'reviews', 'userReview', 'liked', 'saved', 'savedIds', 'related'));
    }
}
