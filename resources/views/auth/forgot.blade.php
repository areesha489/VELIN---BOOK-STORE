@extends('layouts.store')

@section('title', 'Reset password')

@section('content')
    <section class="auth wrap">
        <div>
            <p class="kicker">Password</p>
            <h1>Ask the desk for a reset.</h1>
        </div>
        <form class="panel stack" method="POST" action="{{ route('password.email') }}">
            @csrf
            <label>Email
                <input type="email" name="email" required value="{{ old('email') }}" autocomplete="username">
            </label>
            <button class="btn btn-signal" type="submit">Send reset link</button>
        </form>
    </section>
@endsection
