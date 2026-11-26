@extends('layouts.store')

@section('title', 'Create an account')

@section('content')
    <section class="auth wrap">
        <div class="auth-copy">
            <p class="kicker">New reader</p>
            <h1>Open an account.</h1>
            <p class="lede">A name, an email, and a password of at least eight characters. That is the whole door.</p>
        </div>
        <form class="panel stack" method="POST" action="{{ route('register') }}">
            @csrf
            <label>Name
                <input type="text" name="name" required value="{{ old('name') }}" autocomplete="name">
            </label>
            <label>Email
                <input type="email" name="email" required value="{{ old('email') }}" autocomplete="email">
            </label>
            <label>Phone
                <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="new-password">
            </label>
            <label>Confirm password
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>
            <button class="btn btn-signal" type="submit">Create account</button>
            <p class="fine">Already reading here? <a href="{{ route('login') }}">Sign in</a>.</p>
        </form>
    </section>
@endsection
