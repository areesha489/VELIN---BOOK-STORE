<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::forShelf()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', $term)
                        ->orWhere('isbn', 'like', $term)
                        ->orWhere('subtitle', 'like', $term)
                        ->orWhereHas('author', fn ($author) => $author->where('name', 'like', $term))
                        ->orWhereHas('genre', fn ($genre) => $genre->where('name', 'like', $term));
                });
            })
            ->when($request->filled('genre'), fn ($query) => $query->whereHas(
                'genre',
                fn ($genre) => $genre->where('slug', $request->string('genre'))
            ))
            ->when($request->filled('author'), fn ($query) => $query->whereHas(
                'author',
                fn ($author) => $author->where('slug', $request->string('author'))
            ))
            ->when($request->input('collection') === 'bestsellers', fn ($query) => $query->where('is_bestseller', true))
            ->when($request->input('collection') === 'new', fn ($query) => $query->newArrivals())
            ->tap(function ($query) use ($request) {
                match ($request->input('sort', 'popular')) {
                    'price_asc' => $query->orderBy('price'),
                    'price_desc' => $query->orderByDesc('price'),
                    'newest' => $query->orderByDesc('published_at'),
                    default => $query->orderByDesc('popularity'),
                };
            })
            ->paginate(12)
            ->withQueryString();

        $savedIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('book_id')->all()
            : [];

        return view('store.catalog', [
            'books' => $books,
            'genres' => Genre::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
            'savedIds' => $savedIds,
        ]);
    }
}
