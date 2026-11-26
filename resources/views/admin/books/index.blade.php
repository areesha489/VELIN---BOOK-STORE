@extends('layouts.admin')

@section('title', 'Books')

@section('content')
    <header class="page-head row-head">
        <div>
            <p class="kicker">Catalog</p>
            <h1>Books</h1>
            <p class="fine">Titles, stock, and shelf price for the night catalog.</p>
        </div>
        <a class="btn btn-signal" href="{{ route('admin.books.create') }}">Add a title</a>
    </header>
    <form class="filters" method="GET">
        <label>Search
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Title or ISBN">
        </label>
        <button class="btn btn-ghost" type="submit">Search</button>
    </form>
    <div class="table-wrap panel">
        <table>
            <thead><tr><th>Title</th><th>Author</th><th>Price</th><th>Stock</th><th></th></tr></thead>
            <tbody>
                @foreach($books as $book)
                    <tr>
                        <td><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></td>
                        <td>{{ $book->author->name }}</td>
                        <td>@inr($book->price)</td>
                        <td>{{ $book->stock }}</td>
                        <td class="row-actions">
                            <a href="{{ route('admin.books.edit', $book) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.books.destroy', $book) }}" data-confirm="Remove {{ $book->title }} from the catalog?">
                                @csrf
                                @method('DELETE')
                                <button class="text-btn" type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $books->links() }}
@endsection
