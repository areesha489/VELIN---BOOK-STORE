@extends('layouts.admin')

@section('title', $book->exists ? 'Edit '.$book->title : 'New book')

@section('content')
    <header class="page-head">
        <p class="kicker">Catalog</p>
        <h1>{{ $book->exists ? 'Edit title' : 'New title' }}</h1>
    </header>
    <form class="panel stack" method="POST" enctype="multipart/form-data" action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}">
        @csrf
        @if($book->exists) @method('PUT') @endif
        <div class="form-grid two">
            <label class="span-2">Title
                <input type="text" name="title" required value="{{ old('title', $book->title) }}">
            </label>
            <label class="span-2">Subtitle
                <input type="text" name="subtitle" value="{{ old('subtitle', $book->subtitle) }}">
            </label>
            <label>Author
                <select name="author_id" required>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" @selected((int) old('author_id', $book->author_id) === $author->id)>{{ $author->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Genre
                <select name="genre_id" required>
                    @foreach($genres as $genre)
                        <option value="{{ $genre->id }}" @selected((int) old('genre_id', $book->genre_id) === $genre->id)>{{ $genre->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Price (INR)
                <input type="number" name="price" min="1" step="1" required value="{{ old('price', $book->price) }}">
            </label>
            <label>Compare-at price
                <input type="number" name="compare_at" min="1" step="1" value="{{ old('compare_at', $book->compare_at) }}">
            </label>
            <label>ISBN
                <input type="text" name="isbn" required value="{{ old('isbn', $book->isbn) }}">
            </label>
            <label>Pages
                <input type="number" name="pages" min="1" required value="{{ old('pages', $book->pages) }}">
            </label>
            <label>Language
                <input type="text" name="language" required value="{{ old('language', $book->language) }}">
            </label>
            <label>Format
                <select name="format">
                    @foreach(['Paperback', 'Hardback'] as $format)
                        <option value="{{ $format }}" @selected(old('format', $book->format) === $format)>{{ $format }}</option>
                    @endforeach
                </select>
            </label>
            <label>Published
                <input type="date" name="published_at" required value="{{ old('published_at', optional($book->published_at)->format('Y-m-d')) }}">
            </label>
            <label>Stock
                <input type="number" name="stock" min="0" required value="{{ old('stock', $book->stock ?? 0) }}">
            </label>
            <label>Popularity
                <input type="number" name="popularity" min="0" value="{{ old('popularity', $book->popularity ?? 0) }}">
            </label>
            <label>Cover palette
                <select name="cover_key">
                    @foreach(array_keys(\App\Models\Book::COVERS) as $key)
                        <option value="{{ $key }}" @selected(old('cover_key', $book->cover_key ?: 'ink') === $key)>{{ ucfirst($key) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="span-2">Cover image
                <input type="file" name="cover" accept="image/png,image/jpeg,image/webp">
            </label>
            @if($book->cover_path)
                <label class="check span-2">
                    <input type="checkbox" name="remove_cover" value="1">
                    Remove the uploaded cover and return to the palette
                </label>
            @endif
            <label class="span-2">Excerpt
                <input type="text" name="excerpt" maxlength="400" value="{{ old('excerpt', $book->excerpt) }}">
            </label>
            <label class="span-2">Description
                <textarea name="description" rows="7" required>{{ old('description', $book->description) }}</textarea>
            </label>
            <label class="check">
                <input type="checkbox" name="is_bestseller" value="1" @checked(old('is_bestseller', $book->is_bestseller))>
                Bestseller
            </label>
            <label class="check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->is_featured))>
                Featured on the home page
            </label>
        </div>
        <button class="btn btn-signal" type="submit">{{ $book->exists ? 'Save title' : 'Add title' }}</button>
    </form>
@endsection
