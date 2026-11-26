@extends('layouts.store')

@section('title', 'Out of print')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">404</p>
        <h1>This page has gone out of print.</h1>
        <p class="lede">The shelf does not have that address. The catalog still does.</p>
        <a class="btn btn-signal" href="{{ route('home') }}">Back to the room</a>
    </section>
@endsection
