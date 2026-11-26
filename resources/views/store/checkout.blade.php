@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
    <section class="page-head wrap">
        <p class="kicker">Checkout</p>
        <h1>Send it to a real address</h1>
        <p class="lede">Confirm the door, choose how you pay, and the desk will pack the ticket.</p>
    </section>
    <form class="section wrap checkout" method="POST" action="{{ route('checkout.store') }}" data-reveal>
        @csrf
        <div class="stack">
            <fieldset class="panel">
                <legend>Shipping address</legend>
                @if($addresses->isNotEmpty())
                    <label>Saved address
                        <select name="address_id">
                            <option value="">Use a new address</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->id }}" @selected(old('address_id', $addresses->firstWhere('is_default', true)?->id) == $address->id)>
                                    {{ $address->label }} — {{ $address->line1 }}, {{ $address->city }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <p class="fine">Choose “Use a new address” to save the form below instead.</p>
                @endif
                <div class="form-grid two">
                    <label>Label
                        <input type="text" name="label" value="{{ old('label', 'Home') }}" maxlength="40">
                    </label>
                    <label>Recipient
                        <input type="text" name="recipient" value="{{ old('recipient', auth()->user()->name) }}" autocomplete="name">
                    </label>
                    <label>Phone
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" autocomplete="tel">
                    </label>
                    <label>Postal code
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" autocomplete="postal-code">
                    </label>
                    <label class="span-2">Address line
                        <input type="text" name="line1" value="{{ old('line1') }}" autocomplete="address-line1">
                    </label>
                    <label class="span-2">Address line 2
                        <input type="text" name="line2" value="{{ old('line2') }}" autocomplete="address-line2">
                    </label>
                    <label>City
                        <input type="text" name="city" value="{{ old('city') }}" autocomplete="address-level2">
                    </label>
                    <label>State
                        <input type="text" name="state" value="{{ old('state') }}" autocomplete="address-level1">
                    </label>
                    <label>Country
                        <input type="text" name="country" value="{{ old('country', 'India') }}" autocomplete="country-name">
                    </label>
                    <label class="check">
                        <input type="checkbox" name="address_default" value="1" @checked(old('address_default'))>
                        Make this the default address
                    </label>
                </div>
            </fieldset>

            <fieldset class="panel">
                <legend>Payment</legend>
                <p class="fine">Card numbers are checked, then thrown away. VELIN stores the brand and the last four digits only. Use 4242 4242 4242 4242 if you are rehearsing the desk.</p>
                @if($payments->isNotEmpty())
                    <label>Saved method
                        <select name="payment_method_id">
                            <option value="">Use a new method</option>
                            @foreach($payments as $method)
                                <option value="{{ $method->id }}" @selected(old('payment_method_id', $payments->firstWhere('is_default', true)?->id) == $method->id)>{{ $method->label() }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
                <div class="form-grid two">
                    <label>Type
                        <select name="type">
                            <option value="card" @selected(old('type', 'card') === 'card')>Card</option>
                            <option value="upi" @selected(old('type') === 'upi')>UPI</option>
                        </select>
                    </label>
                    <label>Name
                        <input type="text" name="holder_name" value="{{ old('holder_name', auth()->user()->name) }}" autocomplete="cc-name">
                    </label>
                    <label class="span-2">Card number
                        <input type="text" name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="4242 4242 4242 4242">
                    </label>
                    <label>Expiry month
                        <select name="exp_month">
                            <option value="">Month</option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ $month }}" @selected((int) old('exp_month') === $month)>{{ sprintf('%02d', $month) }}</option>
                            @endfor
                        </select>
                    </label>
                    <label>Expiry year
                        <select name="exp_year">
                            <option value="">Year</option>
                            @for($year = now()->year; $year <= now()->year + 12; $year++)
                                <option value="{{ $year }}" @selected((int) old('exp_year') === $year)>{{ $year }}</option>
                            @endfor
                        </select>
                    </label>
                    <label class="span-2">UPI id
                        <input type="text" name="upi_id" value="{{ old('upi_id') }}" placeholder="name@bank" autocomplete="off">
                    </label>
                    <label class="check span-2">
                        <input type="checkbox" name="payment_default" value="1" @checked(old('payment_default'))>
                        Make this the default method
                    </label>
                </div>
            </fieldset>
        </div>
        <aside class="panel summary">
            <h2>Ticket</h2>
            <ul class="ticket-lines">
                @foreach($cart->items as $item)
                    <li><span>{{ $item->book->title }} × {{ $item->quantity }}</span> <strong>@inr($item->lineTotal())</strong></li>
                @endforeach
            </ul>
            <p><span>Subtotal</span> <strong>@inr($totals['subtotal'])</strong></p>
            <p><span>Dispatch</span> <strong>{{ $totals['shipping'] == 0 ? 'Free' : \App\Support\Money::format($totals['shipping']) }}</strong></p>
            <p class="total"><span>Total</span> <strong>@inr($totals['total'])</strong></p>
            <button class="btn btn-signal" type="submit">Place order</button>
        </aside>
    </form>
@endsection
