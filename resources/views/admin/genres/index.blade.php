@extends('layouts.admin')

@section('title', 'Genres')

@section('content')
    <header class="page-head">
        <p class="kicker">Catalog</p>
        <h1>Genres</h1>
        <p class="fine">Sections that shape browsing and filter rails.</p>
    </header>
    <div class="admin-split">
        <div class="table-wrap panel">
            <table>
                <thead><tr><th>Name</th><th>Titles</th><th></th></tr></thead>
                <tbody>
                    @foreach($genres as $genre)
                        <tr>
                            <td><svg class="swatch" viewBox="0 0 12 12" aria-hidden="true"><rect width="12" height="12" fill="{{ $genre->accent }}"/></svg> {{ $genre->name }}</td>
                            <td>{{ $genre->books_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}" data-confirm="Remove {{ $genre->name }}?">
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
        <form class="panel stack" method="POST" action="{{ route('admin.genres.store') }}">
            @csrf
            <h2>Add a genre</h2>
            <label>Name <input type="text" name="name" required value="{{ old('name') }}"></label>
            <label>Blurb <input type="text" name="blurb" required maxlength="180" value="{{ old('blurb') }}"></label>
            <label>Accent <input type="text" name="accent" required value="{{ old('accent', '#ff4d1c') }}" pattern="#[0-9a-fA-F]{6}"></label>
            <button class="btn btn-signal" type="submit">Add genre</button>
        </form>
    </div>
@endsection
