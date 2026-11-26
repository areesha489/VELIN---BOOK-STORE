@extends('layouts.store')

@section('title', $order->number)

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Order</p>
        <h1>{{ $order->number }}</h1>
        <p class="lede">{{ $order->statusLabel() }} · placed {{ $order->created_at->format('d M Y') }}</p>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <div class="stack">
            @include('partials.timeline')
            <section class="panel">
                <h2>Copies</h2>
                <ul class="ticket-lines">
                    @foreach($order->items as $item)
                        <li>
                            <span>{{ $item->title }} · {{ $item->author_name }} × {{ $item->quantity }}</span>
                            <strong>@inr($item->lineTotal())</strong>
                        </li>
                    @endforeach
                </ul>
                <p><span>Subtotal</span> <strong>@inr($order->subtotal)</strong></p>
                <p><span>Dispatch</span> <strong>{{ (float) $order->shipping === 0.0 ? 'Free' : \App\Support\Money::format($order->shipping) }}</strong></p>
                <p class="total"><span>Total</span> <strong>@inr($order->total)</strong></p>
            </section>
            <section class="panel two-col">
                <div>
                    <h2>Ship to</h2>
                    <p>
                        {{ $order->address_snapshot['recipient'] ?? '' }}<br>
                        {{ $order->address_snapshot['line1'] ?? '' }}<br>
                        @if(!empty($order->address_snapshot['line2'])){{ $order->address_snapshot['line2'] }}<br>@endif
                        {{ $order->address_snapshot['city'] ?? '' }}, {{ $order->address_snapshot['state'] ?? '' }} {{ $order->address_snapshot['postal_code'] ?? '' }}<br>
                        {{ $order->address_snapshot['phone'] ?? '' }}
                    </p>
                </div>
                <div>
                    <h2>Paid with</h2>
                    <p>{{ $order->paymentLabel() }}</p>
                </div>
            </section>
        </div>
    </div>
@endsection
