<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Book::forShelf()->where('is_featured', true)->first()
            ?? Book::forShelf()->orderByDesc('popularity')->first();

        $bestsellers = Book::forShelf()->bestsellers()->orderByDesc('popularity')->take(4)->get();
        $stack = collect([$featured])
            ->filter()
            ->merge($bestsellers->where('id', '!=', $featured?->id))
            ->take(3)
            ->values();

        return view('store.home', [
            'featured' => $featured,
            'stack' => $stack,
            'bestsellers' => $bestsellers,
            'arrivals' => Book::forShelf()->newArrivals()->orderByDesc('published_at')->take(8)->get(),
            'genres' => Genre::query()->withCount('books')->orderBy('name')->get(),
            'titleCount' => Book::query()->count(),
            'savedIds' => $this->savedIds(),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function savedIds(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        return $user->wishlists()->pluck('book_id')->all();
    }
}
