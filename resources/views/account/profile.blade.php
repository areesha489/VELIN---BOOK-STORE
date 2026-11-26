@extends('layouts.store')

@section('title', 'Profile')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Account</p>
        <h1>{{ $user->name }}</h1>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <form class="panel stack" method="POST" action="{{ route('account.update') }}">
            @csrf
            @method('PUT')
            <label>Name
                <input type="text" name="name" required maxlength="120" value="{{ old('name', $user->name) }}" autocomplete="name">
            </label>
            <label>Email
                <input type="email" name="email" required value="{{ old('email', $user->email) }}" autocomplete="email">
            </label>
            <label>Phone
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
            </label>
            <p class="fine">Signed in with {{ $user->provider === 'email' ? 'email' : ucfirst($user->provider) }}. Leave the password fields blank to keep the current one.</p>
            <label>Current password
                <input type="password" name="current_password" autocomplete="current-password">
            </label>
            <label>New password
                <input type="password" name="password" autocomplete="new-password">
            </label>
            <label>Confirm new password
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </label>
            <button class="btn btn-signal" type="submit">Save profile</button>
            <button class="text-btn" type="submit" form="logout-form">Sign out</button>
        </form>
        <form id="logout-form" method="POST" action="{{ route('logout') }}">@csrf</form>
    </div>
@endsection
