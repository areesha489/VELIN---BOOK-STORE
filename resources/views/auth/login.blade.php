@extends('layouts.store')

@section('title', 'Sign in')

@section('content')
    <section class="auth wrap">
        <div class="auth-copy">
            <p class="kicker">The desk</p>
            <h1>Sign in after dark.</h1>
            <p class="lede">Your cart, list, and parcels live on the account. Guests can browse. Readers can buy.</p>
        </div>
        <div class="stack">
            <form class="panel stack" method="POST" action="{{ route('login') }}">
                @csrf
                <label>Email
                    <input type="email" name="email" required autofocus value="{{ old('email') }}" autocomplete="username">
                </label>
                <label>Password
                    <input type="password" name="password" required autocomplete="current-password">
                </label>
                <label class="check">
                    <input type="checkbox" name="remember" value="1">
                    Remember this browser
                </label>
                <button class="btn btn-signal" type="submit">Sign in</button>
                <p class="fine"><a href="{{ route('password.request') }}">Forgot password</a> · <a href="{{ route('register') }}">Create an account</a></p>
            </form>
            @if(app()->environment('local') && !config('services.google.client_id'))
                <div class="demo-desk panel">
                    <p class="kicker">Classroom profiles</p>
                    <p class="fine">These social buttons sign in a local demo profile. They are not a live Google or GitHub handshake.</p>
                    <form method="POST" action="{{ route('social.demo', 'google') }}">
                        @csrf
                        <button class="btn btn-ghost" type="submit">Continue with Google</button>
                    </form>
                    <form method="POST" action="{{ route('social.demo', 'github') }}">
                        @csrf
                        <button class="btn btn-ghost" type="submit">Continue with GitHub</button>
                    </form>
                    <p class="fine">Email desks: reader@velin.store / Reader#2026 · admin@velin.store / VelinAdmin#2026</p>
                </div>
            @endif
        </div>
    </section>
@endsection
