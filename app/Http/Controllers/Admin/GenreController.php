<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        return view('admin.genres.index', [
            'genres' => Genre::query()->withCount('books')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'blurb' => ['required', 'string', 'max:180'],
            'accent' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        Genre::create([
            'name' => $data['name'],
            'slug' => $this->slug($data['name']),
            'blurb' => $data['blurb'],
            'accent' => $data['accent'],
        ]);

        return back()->with('status', 'Genre added.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        try {
            $genre->delete();
        } catch (QueryException) {
            return back()->withErrors(['genre' => 'This genre still has books. Move them first.']);
        }

        return back()->with('status', 'Genre removed.');
    }

    private function slug(string $name): string
    {
        $base = Str::slug($name) ?: 'genre';
        $slug = $base;
        $suffix = 2;

        while (Genre::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
