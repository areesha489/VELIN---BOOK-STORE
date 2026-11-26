@extends('layouts.store')

@section('title', 'Cart')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Cart</p>
        <h1>What you are taking home</h1>
        <p class="lede">Quantities stay editable until checkout. Dispatch turns free once the ticket crosses ₹999.</p>
    </section>
    <section class="section wrap cart-layout" data-stagger>
        @if($cart && $cart->items->isNotEmpty())
            <div class="cart-lines">
                @foreach($cart->items as $item)
                    <article class="cart-line">
                        <a href="{{ route('books.show', $item->book) }}" class="cart-cover">
                            @include('components.cover', ['book' => $item->book])
                        </a>
                        <div>
                            <p class="kicker">{{ $item->book->genre->name ?? '' }}</p>
                            <h2><a href="{{ route('books.show', $item->book) }}">{{ $item->book->title }}</a></h2>
                            <p class="byline">{{ $item->book->author->name }}</p>
                            <p>@inr($item->book->price) each · line @inr($item->lineTotal())</p>
                            <form class="qty" method="POST" action="{{ route('cart.update', $item) }}">
                                @csrf
                                @method('PATCH')
                                <label>Quantity
                                    <input type="number" name="quantity" min="0" max="{{ min(20, $item->book->stock) }}" value="{{ $item->quantity }}">
                                </label>
                                <button class="btn btn-ghost" type="submit">Update</button>
                            </form>
                            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-btn" type="submit">Remove</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
            <aside class="panel summary">
                <h2>Ticket</h2>
                <p><span>Subtotal</span> <strong>@inr($totals['subtotal'])</strong></p>
                <p><span>Dispatch</span> <strong>{{ $totals['shipping'] == 0 ? 'Free' : \App\Support\Money::format($totals['shipping']) }}</strong></p>
                <p class="total"><span>Total</span> <strong>@inr($totals['total'])</strong></p>
                <p class="fine">GST is included. Dispatch is ₹49 until the ticket crosses ₹999.</p>
                @auth
                    <a class="btn btn-signal" href="{{ route('checkout.create') }}">Checkout</a>
                @else
                    <a class="btn btn-signal" href="{{ route('login') }}">Sign in to dispatch</a>
                    <p class="fine">Your cart stays on this browser until you sign in.</p>
                @endauth
            </aside>
        @else
            <div class="empty-panel">
                <p class="kicker">Empty cart</p>
                <h2>Nothing in the bag yet.</h2>
                <p class="lede">The stacks are open. Find a title, press Add, and bring it back here.</p>
                <a class="btn btn-signal" href="{{ route('catalog.index') }}">Browse the catalog</a>
            </div>
        @endif
    </section>
@endsection
