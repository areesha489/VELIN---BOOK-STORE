@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
    <header class="page-head">
        <p class="kicker">Desk</p>
        <h1>Tonight’s room</h1>
        <p class="fine">Live counts for the catalog floor and open parcels.</p>
    </header>
    <div class="stat-grid">
        <article class="stat">
            <p class="kicker">Titles</p>
            <strong data-countup="{{ $bookCount }}">{{ $bookCount }}</strong>
            <span class="stat-help">In the catalog</span>
        </article>
        <article class="stat">
            <p class="kicker">Open orders</p>
            <strong data-countup="{{ $openOrders }}">{{ $openOrders }}</strong>
            <span class="stat-help">{{ $orderCount }} total placed</span>
        </article>
        <article class="stat">
            <p class="kicker">Readers</p>
            <strong data-countup="{{ $readerCount }}">{{ $readerCount }}</strong>
            <span class="stat-help">Active accounts</span>
        </article>
        <article class="stat">
            <p class="kicker">Revenue</p>
            <strong data-countup="{{ (int) $revenue }}" data-prefix="₹">@inr($revenue)</strong>
            <span class="stat-help">Excluding cancelled</span>
        </article>
    </div>
    <div class="admin-split">
        <section class="panel">
            <div class="panel-head">
                <h2>Recent orders</h2>
                <a class="panel-link" href="{{ route('admin.orders.index') }}">View all</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Number</th><th>Reader</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a></td>
                                <td>{{ $order->user->name }}</td>
                                <td><span class="pill pill-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                                <td>@inr($order->total)</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="panel">
            <div class="panel-head">
                <h2>Low stock</h2>
                <a class="panel-link" href="{{ route('admin.books.index') }}">Manage books</a>
            </div>
            <ul class="stock-list">
                @forelse($lowStock as $book)
                    <li>
                        <a href="{{ route('admin.books.edit', $book) }}">{{ $book->title }}</a>
                        <span class="stock-meta">{{ $book->stock }} left · Edit</span>
                    </li>
                @empty
                    <li class="stock-empty">Every title has more than five copies.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
