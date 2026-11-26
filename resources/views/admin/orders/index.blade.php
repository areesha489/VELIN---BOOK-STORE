@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <header class="page-head">
        <p class="kicker">Floor</p>
        <h1>Orders</h1>
        <p class="fine">Track parcels, status, and totals across the desk.</p>
    </header>
    <form class="filters" method="GET">
        <label>Search
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Number, name, or email">
        </label>
        <label>Status
            <select name="status">
                <option value="">All</option>
                @foreach(\App\Models\Order::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn btn-ghost" type="submit">Filter</button>
    </form>
    <div class="table-wrap panel">
        <table>
            <thead><tr><th>Number</th><th>Reader</th><th>Status</th><th>Total</th><th>Placed</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a></td>
                        <td>{{ $order->user->name }}</td>
                        <td><span class="pill pill-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                        <td>@inr($order->total)</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No orders in this cut.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
@endsection
