<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        return view('admin.authors.index', [
            'authors' => Author::query()->withCount('books')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'origin' => ['nullable', 'string', 'max:120'],
            'bio' => ['required', 'string', 'max:1000'],
        ]);

        Author::create([
            'name' => $data['name'],
            'slug' => $this->slug($data['name']),
            'origin' => $data['origin'] ?? null,
            'bio' => $data['bio'],
        ]);

        return back()->with('status', 'Author added.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        try {
            $author->delete();
        } catch (QueryException) {
            return back()->withErrors(['author' => 'This writer still has books on the shelf.']);
        }

        return back()->with('status', 'Author removed.');
    }

    private function slug(string $name): string
    {
        $base = Str::slug($name) ?: 'author';
        $slug = $base;
        $suffix = 2;

        while (Author::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
