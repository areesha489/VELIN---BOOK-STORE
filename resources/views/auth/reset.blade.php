@extends('layouts.store')

@section('title', 'Choose a new password')

@section('content')
    <section class="auth wrap">
        <div>
            <p class="kicker">Password</p>
            <h1>Choose a new password.</h1>
        </div>
        <form class="panel stack" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label>Email
                <input type="email" name="email" required value="{{ old('email', $email) }}">
            </label>
            <label>New password
                <input type="password" name="password" required autocomplete="new-password">
            </label>
            <label>Confirm password
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>
            <button class="btn btn-signal" type="submit">Update password</button>
        </form>
    </section>
@endsection
