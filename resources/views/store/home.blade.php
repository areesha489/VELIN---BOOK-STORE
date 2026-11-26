@extends('layouts.store')

@section('title', 'After-dark bookselling')
@section('description', 'VELIN is a night bookstore in Mumbai. Bestsellers, new arrivals, and a catalog edited to be carried home.')

@section('content')
    <section class="hero wrap">
        <p class="hero-ghost" aria-hidden="true">Night</p>
        <div class="hero-copy">
            <p class="kicker">Reading room · Mumbai</p>
            <h1>
                <span class="hero-line">Books for the</span>
                <span class="hero-line">hours after</span>
                <span class="hero-line hero-accent">dark.</span>
            </h1>
            <p class="lede">A small catalog, edited hard. Search the stacks, keep a private list, and follow a parcel from the bindery to your door.</p>
            <div class="hero-actions">
                <a class="btn btn-signal" href="{{ route('catalog.index') }}">Enter the stacks</a>
                <a class="btn btn-ghost" href="{{ route('catalog.index', ['collection' => 'bestsellers']) }}">Bestsellers</a>
            </div>
            <ul class="hero-facts">
                <li><strong>{{ $titleCount }}</strong><span>titles on the shelf</span></li>
                <li><strong>₹999</strong><span>free dispatch after</span></li>
                <li><strong>Kala Ghoda</strong><span>the reading room</span></li>
            </ul>
            @if($featured)
                <p class="fine">Tonight’s open book: <a href="{{ route('books.show', $featured) }}">{{ $featured->title }}</a> by {{ $featured->author->name }}.</p>
            @endif
        </div>
        <div class="hero-aside" data-tilt>
            @forelse($stack as $book)
                <a class="stack-book stack-{{ $loop->index }}" href="{{ route('books.show', $book) }}">
                    @include('components.cover', ['book' => $book])
                </a>
            @empty
                <div class="poster">The stacks are being set.</div>
            @endforelse
        </div>
    </section>

    @if($genres->isNotEmpty())
        <div class="marquee" aria-hidden="true">
            <div class="marquee-track">
                @foreach([1, 2] as $copy)
                    <div class="marquee-set">
                        @foreach($genres as $genre)
                            <span>{{ $genre->name }}</span>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <section class="section wrap" data-reveal>
        <div class="section-head">
            <div>
                <p class="kicker">01 — Moving</p>
                <h2>Bestsellers</h2>
            </div>
            <a class="text-link" href="{{ route('catalog.index', ['collection' => 'bestsellers']) }}">See the list</a>
        </div>
        @if($bestsellers->isNotEmpty())
            <div class="shelf" data-stagger>
                @foreach($bestsellers as $book)
                    <x-book-card :book="$book" :saved="in_array($book->id, $savedIds)" />
                @endforeach
            </div>
        @else
            <p class="empty">No bestsellers yet. The desk is still choosing.</p>
        @endif
    </section>

    <section class="manifesto" data-reveal>
        <div class="split wrap">
        <blockquote>
            <p>Technology moves fast. A good shelf does not. VELIN is the part you can hold: one catalog, one cart, one parcel you can actually track.</p>
            <footer>From the Ropewalk desk</footer>
        </blockquote>
        <div>
            <p class="kicker">02 — How a copy leaves</p>
            <ol class="steps">
                <li><strong>Find it.</strong> Search by title, author, or genre. Sort by price, popularity, or release date.</li>
                <li><strong>Keep it.</strong> A list for later, a cart for tonight, an address the desk can trust.</li>
                <li><strong>Follow it.</strong> From placed to delivered, with a carrier code once it leaves the room.</li>
            </ol>
        </div>
        </div>
    </section>

    <section class="section wrap" data-reveal>
        <div class="section-head">
            <div>
                <p class="kicker">03 — Just in</p>
                <h2>New arrivals</h2>
            </div>
            <a class="text-link" href="{{ route('catalog.index', ['collection' => 'new']) }}">All new copies</a>
        </div>
        <p class="rail-hint">Slide the shelf sideways.</p>
        @if($arrivals->isNotEmpty())
            <div class="rail-shell">
                <div class="rail" data-stagger>
                    @foreach($arrivals as $book)
                        <x-book-card :book="$book" :saved="in_array($book->id, $savedIds)" />
                    @endforeach
                </div>
            </div>
        @else
            <p class="empty">New arrivals land here after the desk dates them.</p>
        @endif
    </section>

    <section class="section wrap" data-reveal>
        <div class="section-head">
            <div>
                <p class="kicker">04 — Shelves</p>
                <h2>By genre</h2>
            </div>
        </div>
        <div class="genre-index">
            @foreach($genres as $genre)
                <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}">
                    <span><em>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</em>{{ $genre->name }}</span>
                    <small>{{ $genre->books_count }} {{ \Illuminate\Support\Str::plural('title', $genre->books_count) }}</small>
                </a>
            @endforeach
        </div>
    </section>
@endsection
