@extends('layouts.store')

@section('title', request('collection') === 'bestsellers' ? 'Bestsellers' : (request('collection') === 'new' ? 'New arrivals' : 'The stacks'))

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Catalog</p>
        <h1>
            @if(request('collection') === 'bestsellers') Bestsellers
            @elseif(request('collection') === 'new') New arrivals
            @else The stacks
            @endif
        </h1>
        <p class="lede">{{ $books->total() }} {{ \Illuminate\Support\Str::plural('title', $books->total()) }} in this cut of the room.</p>
    </section>

    <div class="filters-bar wrap">
        <form class="filters" method="GET" action="{{ route('catalog.index') }}" data-auto-submit>
            <label>Search
                <input id="search" type="search" name="q" value="{{ request('q') }}" placeholder="Title, author, genre, ISBN">
            </label>
            <label>Genre
                <select name="genre">
                    <option value="">All genres</option>
                    @foreach($genres as $genre)
                        <option value="{{ $genre->slug }}" @selected(request('genre') === $genre->slug)>{{ $genre->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Author
                <select name="author">
                    <option value="">All authors</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->slug }}" @selected(request('author') === $author->slug)>{{ $author->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Sort
                <select name="sort">
                    <option value="popular" @selected(request('sort', 'popular') === 'popular')>Popularity</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price · low to high</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price · high to low</option>
                    <option value="newest" @selected(request('sort') === 'newest')>Release date</option>
                </select>
            </label>
            <input type="hidden" name="collection" value="{{ request('collection') }}">
            <button class="btn btn-signal" type="submit">Apply</button>
            <a class="text-link" href="{{ route('catalog.index') }}">Reset</a>
        </form>

        <div class="chips">
            @foreach(['' => 'All', 'bestsellers' => 'Bestsellers', 'new' => 'New arrivals'] as $value => $label)
                <a @class(['chip', 'is-active' => (string) request('collection') === (string) $value]) href="{{ route('catalog.index', array_merge(request()->except(['page', 'collection']), $value ? ['collection' => $value] : [])) }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <section class="section wrap" data-reveal>
        @if($books->count())
            <div class="shelf" data-stagger>
                @foreach($books as $book)
                    <x-book-card :book="$book" :saved="in_array($book->id, $savedIds)" />
                @endforeach
            </div>
            {{ $books->links() }}
        @else
            <div class="empty-panel">
                <p class="kicker">No match</p>
                <h2>Nothing in this cut.</h2>
                <p class="lede">Try another name, clear a filter, or reset the stacks and start again.</p>
                <a class="btn btn-signal" href="{{ route('catalog.index') }}">Reset the stacks</a>
            </div>
        @endif
    </section>
@endsection
