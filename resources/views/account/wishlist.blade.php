@extends('layouts.store')

@section('title', 'Wishlist')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Account</p>
        <h1>Wishlist</h1>
        <p class="lede">Titles you mean to return to — kept off the cart until you are ready.</p>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <div class="account-panel">
            @if($books->isNotEmpty())
                <p class="shelf-count">{{ $books->count() }} {{ Str::plural('title', $books->count()) }} saved</p>
                <div class="shelf">
                    @foreach($books as $book)
                        <x-book-card :book="$book" :saved="true" />
                    @endforeach
                </div>
            @else
                <div class="empty-panel">
                    <p class="kicker">Nothing saved</p>
                    <h2>The list is quiet.</h2>
                    <p class="lede">Walk the stacks, open a title that holds, and press Save. It will wait here.</p>
                    <a class="btn btn-signal" href="{{ route('catalog.index') }}">Walk the stacks</a>
                </div>
            @endif
        </div>
    </div>
@endsection
