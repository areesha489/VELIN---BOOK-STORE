<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::query()
            ->with(['author:id,name', 'genre:id,name'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where('title', 'like', $term)->orWhere('isbn', 'like', $term);
            })
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('admin.books.index', ['books' => $books]);
    }

    public function create(): View
    {
        return view('admin.books.form', [
            'book' => new Book(['language' => 'English', 'format' => 'Paperback', 'cover_key' => 'ink', 'stock' => 10]),
            'authors' => Author::query()->orderBy('name')->get(),
            'genres' => Genre::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $book = new Book($this->payload($request));
        $book->slug = $this->slug($book->title);
        $this->storeCover($request, $book);
        $book->save();
        $this->singleFeature($book);

        return redirect()->route('admin.books.index')->with('status', $book->title.' is on the shelf.');
    }

    public function edit(Book $book): View
    {
        return view('admin.books.form', [
            'book' => $book,
            'authors' => Author::query()->orderBy('name')->get(),
            'genres' => Genre::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $book->fill($this->payload($request, $book));
        $this->storeCover($request, $book);
        $book->save();
        $this->singleFeature($book);

        return redirect()->route('admin.books.index')->with('status', $book->title.' updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $title = $book->title;
        $book->delete();

        return back()->with('status', $title.' removed from the catalog.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, ?Book $book = null): array
    {
        $request->merge([
            'compare_at' => $request->filled('compare_at') ? $request->input('compare_at') : null,
            'subtitle' => $request->filled('subtitle') ? $request->input('subtitle') : null,
            'excerpt' => $request->filled('excerpt') ? $request->input('excerpt') : null,
        ]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'author_id' => ['required', 'exists:authors,id'],
            'genre_id' => ['required', 'exists:genres,id'],
            'description' => ['required', 'string', 'max:8000'],
            'excerpt' => ['nullable', 'string', 'max:400'],
            'price' => ['required', 'numeric', 'min:1', 'max:100000'],
            'compare_at' => ['nullable', 'numeric', 'min:1', 'gt:price'],
            'isbn' => ['required', 'string', 'max:24', 'unique:books,isbn'.($book ? ','.$book->id : '')],
            'pages' => ['required', 'integer', 'min:1', 'max:5000'],
            'language' => ['required', 'string', 'max:40'],
            'format' => ['required', 'in:Paperback,Hardback'],
            'published_at' => ['required', 'date'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'popularity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'cover_key' => ['required', 'in:'.implode(',', array_keys(Book::COVERS))],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($data['cover']);

        $data['is_bestseller'] = $request->boolean('is_bestseller');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['popularity'] = $data['popularity'] ?? $book?->popularity ?? 0;

        return $data;
    }

    private function storeCover(Request $request, Book $book): void
    {
        if ($request->boolean('remove_cover') && $book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
            $book->cover_path = null;
        }

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $book->cover_path = $request->file('cover')->store('covers', 'public');
        }
    }

    private function singleFeature(Book $book): void
    {
        if ($book->is_featured) {
            Book::query()->where('id', '!=', $book->id)->update(['is_featured' => false]);
        }
    }

    private function slug(string $title): string
    {
        $base = Str::slug($title) ?: 'book';
        $slug = $base;
        $suffix = 2;

        while (Book::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
