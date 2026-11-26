@props(['book', 'saved' => false])
<article class="book-card">
    <a class="book-card-media" href="{{ route('books.show', $book) }}">
        @include('components.cover', ['book' => $book])
        @if($book->is_bestseller)
            <span class="badge">Bestseller</span>
        @elseif($book->isNew())
            <span class="badge badge-lime">New</span>
        @endif
    </a>
    <div class="book-card-body">
        <p class="kicker">{{ $book->genre->name }}</p>
        <h3><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></h3>
        <p class="byline">{{ $book->author->name }}</p>
        <x-stars :rating="$book->reviews_avg_rating" :count="$book->reviews_count" />
        <div class="book-card-row">
            <p class="price">
                @if($book->onSale())
                    <span class="price-was">@inr($book->compare_at)</span>
                @endif
                @inr($book->price)
            </p>
            <div class="card-actions">
                @auth
                    <form method="POST" action="{{ route('wishlist.toggle') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button class="text-btn" type="submit">{{ $saved ? 'Saved' : 'Save' }}</button>
                    </form>
                @endauth
                @if($book->stock > 0)
                    <form method="POST" action="{{ route('cart.store') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn btn-signal" type="submit">Add</button>
                    </form>
                @else
                    <span class="fine">Out of copies</span>
                @endif
            </div>
        </div>
    </div>
</article>
