@extends('layouts.store')

@section('title', 'Addresses')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Account</p>
        <h1>Shipping addresses</h1>
        <p class="lede">Doors the desk can trust when a parcel leaves Ropewalk Lane.</p>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <div class="stack account-panel" data-stagger>
            @forelse($addresses as $address)
                <article class="panel">
                    <p class="kicker">{{ $address->label }} @if($address->is_default) · Default @endif</p>
                    <p>{!! nl2br(e($address->block())) !!}</p>
                    <a class="text-link" href="{{ route('addresses.edit', $address) }}">Edit</a>
                    <form method="POST" action="{{ route('addresses.destroy', $address) }}" data-confirm="Remove this address?">
                        @csrf
                        @method('DELETE')
                        <button class="text-btn" type="submit">Remove</button>
                    </form>
                </article>
            @empty
                <div class="empty-panel">
                    <p class="kicker">No door yet</p>
                    <h2>Add the first address.</h2>
                    <p class="lede">Use the form below so checkout already knows where to send the ticket.</p>
                </div>
            @endforelse
            <form class="panel stack" method="POST" action="{{ route('addresses.store') }}">
                @csrf
                <h2>New address</h2>
                @include('account.partials.address-fields')
                <button class="btn btn-signal" type="submit">Save address</button>
            </form>
        </div>
    </div>
@endsection
