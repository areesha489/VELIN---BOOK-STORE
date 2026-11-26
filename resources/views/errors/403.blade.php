@extends('layouts.store')

@section('title', 'Closed room')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">403</p>
        <h1>This room is for editors.</h1>
        <p class="lede">The desk is locked from this account.</p>
        <a class="btn btn-signal" href="{{ route('home') }}">Return to the shop</a>
    </section>
@endsection
