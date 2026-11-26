@extends('layouts.store')

@section('title', 'Payments')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Account</p>
        <h1>Payment methods</h1>
        <p class="lede">We never store a full card number. A rehearsal card is 4242 4242 4242 4242.</p>
    </section>
    <div class="account wrap section">
        @include('partials.account-nav')
        <div class="stack account-panel" data-stagger>
            @forelse($methods as $method)
                <article class="panel method">
                    <div>
                        <p class="kicker">{{ $method->is_default ? 'Default' : 'Saved' }}</p>
                        <h2>{{ $method->label() }}</h2>
                        @if($method->holder_name)
                            <p>{{ $method->holder_name }}</p>
                        @endif
                    </div>
                    <div class="card-actions">
                        @if(!$method->is_default)
                            <form method="POST" action="{{ route('payments.default', $method) }}">
                                @csrf
                                @method('PATCH')
                                <button class="text-btn" type="submit">Make default</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('payments.destroy', $method) }}" data-confirm="Remove this payment method?">
                            @csrf
                            @method('DELETE')
                            <button class="text-btn" type="submit">Remove</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-panel">
                    <p class="kicker">No card yet</p>
                    <h2>No payment method saved.</h2>
                    <p class="lede">Add a rehearsal card or UPI id below. Full card numbers are never kept.</p>
                </div>
            @endforelse
            <form class="panel stack" method="POST" action="{{ route('payments.store') }}">
                @csrf
                <h2>Add a method</h2>
                <div class="form-grid two">
                    <label>Type
                        <select name="type">
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                        </select>
                    </label>
                    <label>Name
                        <input type="text" name="holder_name" value="{{ old('holder_name', auth()->user()->name) }}">
                    </label>
                    <label class="span-2">Card number
                        <input type="text" name="card_number" inputmode="numeric" autocomplete="cc-number">
                    </label>
                    <label>Expiry month
                        <select name="exp_month">
                            <option value="">Month</option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ $month }}">{{ sprintf('%02d', $month) }}</option>
                            @endfor
                        </select>
                    </label>
                    <label>Expiry year
                        <select name="exp_year">
                            <option value="">Year</option>
                            @for($year = now()->year; $year <= now()->year + 12; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </label>
                    <label class="span-2">UPI id
                        <input type="text" name="upi_id" value="{{ old('upi_id') }}" placeholder="name@bank">
                    </label>
                    <label class="check span-2">
                        <input type="checkbox" name="is_default" value="1">
                        Make default
                    </label>
                </div>
                <button class="btn btn-signal" type="submit">Save method</button>
            </form>
        </div>
    </div>
@endsection
