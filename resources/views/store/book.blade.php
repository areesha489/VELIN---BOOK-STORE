@extends('layouts.store')

@section('title', $book->title)
@section('description', $book->excerpt ?: \Illuminate\Support\Str::limit($book->description, 150))

@section('content')
    <article class="detail wrap">
        <div class="detail-cover">
            @include('components.cover', ['book' => $book])
        </div>
        <div class="detail-copy">
            <p class="kicker"><a href="{{ route('catalog.index', ['genre' => $book->genre->slug]) }}">{{ $book->genre->name }}</a></p>
            <h1 class="detail-title">{{ $book->title }}</h1>
            @if($book->subtitle)
                <p class="lede">{{ $book->subtitle }}</p>
            @endif
            <p class="byline"><a href="{{ route('catalog.index', ['author' => $book->author->slug]) }}">{{ $book->author->name }}</a> · {{ $book->author->origin }}</p>
            <x-stars :rating="$book->reviews_avg_rating" :count="$book->reviews_count" />
            <p class="price price-lg">
                @if($book->onSale())
                    <span class="price-was">@inr($book->compare_at)</span>
                @endif
                @inr($book->price)
            </p>
            <p class="meta">
                {{ $book->format }} · {{ $book->pages }} pages · {{ $book->language }} · {{ $book->published_at->format('Y') }}
                <br>ISBN {{ $book->isbn }}
            </p>
            <p class="stock {{ $book->stock === 0 ? 'is-out' : ($book->stock <= 5 ? 'is-low' : '') }}">
                @if($book->stock === 0)
                    Out of copies in this room.
                @elseif($book->stock <= 5)
                    Only {{ $book->stock }} {{ \Illuminate\Support\Str::plural('copy', $book->stock) }} left.
                @else
                    In the stacks.
                @endif
            </p>

            <div class="detail-actions">
                @if($book->stock > 0)
                    <form class="buy-row" method="POST" action="{{ route('cart.store') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <label>Quantity
                            <input type="number" name="quantity" min="1" max="{{ min(20, $book->stock) }}" value="1">
                        </label>
                        <button class="btn btn-signal" type="submit">Add to cart</button>
                    </form>
                @endif

                @auth
                    <form method="POST" action="{{ route('wishlist.toggle') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button class="btn btn-ghost" type="submit">{{ $saved ? 'Saved' : 'Save to wishlist' }}</button>
                    </form>
                @else
                    <p class="fine"><a href="{{ route('login') }}">Sign in</a> to save this title or leave a note.</p>
                @endauth
            </div>

            @if($book->excerpt)
                <blockquote class="excerpt">{{ $book->excerpt }}</blockquote>
            @endif
            <div class="prose">{!! nl2br(e($book->description)) !!}</div>
            <p class="fine">{{ $book->author->bio }}</p>
        </div>
    </article>

    <section class="section wrap" id="reviews" data-reveal>
        <div class="section-head">
            <div>
                <p class="kicker">Notes from readers</p>
                <h2>Ratings and reviews</h2>
            </div>
        </div>
        @auth
            <form class="panel review-form" method="POST" action="{{ route('reviews.store', $book) }}">
                @csrf
                <h3>{{ $userReview ? 'Update your note' : 'Leave a note' }}</h3>
                <fieldset class="rate">
                    <legend>Rating</legend>
                    @for($score = 1; $score <= 5; $score++)
                        <label class="rate-opt">
                            <input type="radio" name="rating" value="{{ $score }}" @checked((int) old('rating', $userReview->rating ?? 0) === $score) required>
                            <span>{{ $score }} <span class="sr-only">{{ \Illuminate\Support\Str::plural('star', $score) }}</span></span>
                        </label>
                    @endfor
                </fieldset>
                <label>Title
                    <input type="text" name="title" maxlength="120" required value="{{ old('title', $userReview->title ?? '') }}">
                </label>
                <label>Review
                    <textarea name="body" minlength="20" maxlength="2000" required rows="5">{{ old('body', $userReview->body ?? '') }}</textarea>
                </label>
                <button class="btn btn-paper" type="submit">{{ $userReview ? 'Save note' : 'Publish note' }}</button>
            </form>
        @endauth

        @forelse($reviews as $review)
            <article class="review">
                <header>
                    <strong>{{ $review->user->name }}</strong>
                    <x-stars :rating="$review->rating" />
                </header>
                <h3>{{ $review->title }}</h3>
                <p>{!! nl2br(e($review->body)) !!}</p>
                <div class="review-actions">
                    @auth
                        @if(auth()->id() !== $review->user_id)
                            <form method="POST" action="{{ route('reviews.like', $review) }}">
                                @csrf
                                <button class="text-btn" type="submit" aria-pressed="{{ in_array($review->id, $liked, true) ? 'true' : 'false' }}">
                                    {{ in_array($review->id, $liked, true) ? 'Liked' : 'Like' }} · {{ $review->likes_count }}
                                </button>
                            </form>
                        @else
                            <span class="fine">{{ $review->likes_count }} {{ \Illuminate\Support\Str::plural('like', $review->likes_count) }}</span>
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" data-confirm="Remove your review?">
                                @csrf
                                @method('DELETE')
                                <button class="text-btn" type="submit">Delete</button>
                            </form>
                        @endif
                    @else
                        <span class="fine">{{ $review->likes_count }} {{ \Illuminate\Support\Str::plural('like', $review->likes_count) }}</span>
                    @endauth
                </div>
            </article>
        @empty
            <div class="empty-panel">
                <p class="kicker">Quiet shelf</p>
                <h2>No notes yet.</h2>
                <p class="lede">Be the first reader to leave a mark on this title.</p>
            </div>
        @endforelse
    </section>

    @if($related->isNotEmpty())
        <section class="section wrap" data-reveal>
            <div class="section-head">
                <h2>More {{ $book->genre->name }}</h2>
            </div>
            <div class="shelf" data-stagger>
                @foreach($related as $item)
                    <x-book-card :book="$item" :saved="in_array($item->id, $savedIds)" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
