@extends('layouts.admin')

@section('title', 'Authors')

@section('content')
    <header class="page-head">
        <p class="kicker">Catalog</p>
        <h1>Authors</h1>
        <p class="fine">Writers attached to titles on the floor.</p>
    </header>
    <div class="admin-split">
        <div class="table-wrap panel">
            <table>
                <thead><tr><th>Name</th><th>Origin</th><th>Titles</th><th></th></tr></thead>
                <tbody>
                    @foreach($authors as $author)
                        <tr>
                            <td>{{ $author->name }}</td>
                            <td>{{ $author->origin }}</td>
                            <td>{{ $author->books_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" data-confirm="Remove {{ $author->name }}?">
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
        <form class="panel stack" method="POST" action="{{ route('admin.authors.store') }}">
            @csrf
            <h2>Add an author</h2>
            <label>Name <input type="text" name="name" required value="{{ old('name') }}"></label>
            <label>Origin <input type="text" name="origin" value="{{ old('origin') }}"></label>
            <label>Bio <textarea name="bio" rows="4" required>{{ old('bio') }}</textarea></label>
            <button class="btn btn-signal" type="submit">Add author</button>
        </form>
    </div>
@endsection
