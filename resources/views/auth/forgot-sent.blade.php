@extends('layouts.store')

@section('title', 'Check your email')

@section('content')
    <section class="auth wrap">
        <div class="panel stack">
            <p class="kicker">Password</p>
            <h1>If that account exists, the link is on its way.</h1>
            <p>Check the inbox for that address. The message comes from the VELIN desk.</p>
            @if($demoUrl)
                <div class="demo-desk">
                    <p class="kicker">Local mail</p>
                    <p>This machine writes mail to the log and also shows the link here so the classroom can continue without an inbox.</p>
                    <p><a href="{{ $demoUrl }}">Reset this password</a></p>
                </div>
            @endif
            <a class="text-link" href="{{ route('login') }}">Back to sign in</a>
        </div>
    </section>
@endsection
