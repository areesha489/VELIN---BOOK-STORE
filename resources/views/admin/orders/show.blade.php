@extends('layouts.admin')

@section('title', $order->number)

@section('content')
    <header class="page-head">
        <p class="kicker">{{ $order->user->name }} · {{ $order->user->email }}</p>
        <h1>{{ $order->number }}</h1>
    </header>
    <div class="admin-split">
        <div class="stack">
            @include('partials.timeline')
            <section class="panel">
                <h2>Copies</h2>
                <ul class="ticket-lines">
                    @foreach($order->items as $item)
                        <li><span>{{ $item->title }} × {{ $item->quantity }}</span> <strong>@inr($item->lineTotal())</strong></li>
                    @endforeach
                </ul>
                <p class="total"><span>Total</span> <strong>@inr($order->total)</strong></p>
                <p>{{ $order->paymentLabel() }}</p>
            </section>
        </div>
        <form class="panel stack" method="POST" action="{{ route('admin.orders.update', $order) }}">
            @csrf
            @method('PUT')
            <h2>Update status</h2>
            <label>Status
                <select name="status">
                    @foreach(\App\Models\Order::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Carrier
                <input type="text" name="carrier" value="{{ old('carrier', $order->carrier) }}" placeholder="Delhivery">
            </label>
            <label>Tracking code
                <input type="text" name="tracking_code" value="{{ old('tracking_code', $order->tracking_code) }}">
            </label>
            <label>Note for the timeline
                <textarea name="note" rows="3">{{ old('note') }}</textarea>
            </label>
            <button class="btn btn-signal" type="submit">Save update</button>
        </form>
    </div>
@endsection
