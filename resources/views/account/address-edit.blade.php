@extends('layouts.store')

@section('title', 'Edit address')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Account</p>
        <h1>Edit address</h1>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <form class="panel stack" method="POST" action="{{ route('addresses.update', $address) }}">
            @csrf
            @method('PUT')
            @include('account.partials.address-fields', ['address' => $address])
            <button class="btn btn-signal" type="submit">Update address</button>
        </form>
    </div>
@endsection
