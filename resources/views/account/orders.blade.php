@extends('layouts.store')

@section('title', 'Orders')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Account</p>
        <h1>Order history</h1>
        <p class="lede">Tickets, status, and tracking for everything that left the desk.</p>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <div class="account-panel" data-stagger>
            @forelse($orders as $order)
                <a class="order-row" href="{{ route('orders.show', $order) }}">
                    <span>
                        <strong>{{ $order->number }}</strong>
                        <small>{{ $order->created_at->format('d M Y') }}</small>
                    </span>
                    <span class="pill pill-{{ $order->status }}">{{ $order->statusLabel() }}</span>
                    <strong>@inr($order->total)</strong>
                </a>
            @empty
                <div class="empty-panel">
                    <p class="kicker">No parcels</p>
                    <h2>No orders yet.</h2>
                    <p class="lede">When you check out, the ticket and its timeline will live here.</p>
                    <a class="btn btn-signal" href="{{ route('catalog.index') }}">Find a book</a>
                </div>
            @endforelse
            {{ $orders->links() }}
        </div>
    </div>
@endsection
